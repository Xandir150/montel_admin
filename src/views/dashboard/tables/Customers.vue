<template>
  <v-container
    id="customers"
    fluid
    tag="section"
  >
    <v-dialog
      v-model="dialogChangeTariff"
      max-width="300"
    >
      <v-card>
        <v-card-title>
          Do you really want to change the tariff?

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="close"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text class="pb-6 pt-12 text-center">
          <v-btn
            class="mr-3"
            text
            @click="close"
          >
            No
          </v-btn>

          <v-btn
            color="success"
            text
            @click="confirmChangeTariff"
          >
            Yes
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogChangeBalance"
      max-width="350"
    >
      <v-card>
        <v-card-title>
          <span class="headline"> {{ formTitleChangeBalance }} баланса </span>
          <v-spacer />
        </v-card-title>
        <v-card-text class="pb-6 pt-12 text-center">
          <v-text-field
            v-model="editedItem.name"
            readonly
            label="Клиенту"
          />
          <v-text-field
            v-model="editedItem.phone"
            readonly
            label="Номер"
          />
          <v-text-field
            v-model="editedItem.balance"
            :rules="rules.digits"
            label="Сумма"
            suffix="€"
          />
          <v-select
            v-if="editedItem.route !== 0"
            v-model="editedItem.place"
            :items="places"
            :rules="rules.name"
            label="Способ оплаты"
            required
          />
          <v-text-field
            v-model="editedItem.description"
            :rules="rules.name"
            required
            label="Комментарий"
            counter
            maxlength="64"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            :disabled="!formIsValid"
            color="success"
            text
            @click="changeBalance()"
          >
            Yes
          </v-btn>
          <v-btn
            class="mr-3"
            text
            @click="close"
          >
            No
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialogTransBalance"
      max-width="600"
      persistent
    >
      <v-card>
        <v-card-title>
          <span class="headline"> Перенос баланса </span>
          <v-spacer />
        </v-card-title>
        <v-card-text class="pb-6 pt-12 text-center">
          <v-row>
            <v-col>
              <v-text-field
                v-model="editedItem.name"
                readonly
                label="От клиента"
              />
            </v-col>
            <v-col
              align-self="center"
              cols="1"
            >
              <v-icon>
                mdi-arrow-right
              </v-icon>
            </v-col>
            <v-col>
              <v-text-field
                v-model="editedItem.toName"
                readonly
                required
                label="Для клиента"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-text-field
                v-model="editedItem.phone"
                readonly
                label="С номера"
              />
            </v-col>
            <v-col
              align-self="center"
              cols="1"
            >
              <v-icon>
                mdi-arrow-right
              </v-icon>
            </v-col>
            <v-col>
              <v-text-field
                v-model="editedItem.toPhone"
                :rules="rules.number"
                required
                autofocus
                label="На номер"
                @input="searchByPhone"
              />
            </v-col>
          </v-row>
          <v-row v-if="editedItem.toName">
            <v-col>
              <v-text-field
                v-model="editedItem.balance"
                readonly
                suffix="€"
              />
            </v-col>
            <v-col
              align-self="center"
              cols="1"
            >
              <v-icon>
                mdi-minus
              </v-icon>
            </v-col>
            <v-col>
              <v-text-field
                v-model="editedItem.toAmount"
                :rules="rules.digits"
                required
                bolt
                label="Сумма"
                suffix="€"
                @input="calcNewBalance"
              />
            </v-col>
            <v-col
              align-self="center"
              cols="1"
            >
              <v-icon>
                mdi-arrow-right
              </v-icon>
            </v-col>
            <v-col>
              <v-text-field
                v-model="editedItem.toBalance"
                readonly
                suffix="€"
              />
            </v-col>
          </v-row>
          <v-text-field
            v-if="editedItem.toName"
            v-model="editedItem.description"
            :rules="rules.name"
            required
            label="Комментарий"
            counter
            maxlength="64"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            :disabled="!formTransIsValid"
            color="success"
            text
            @click="transBalanceSend()"
          >
            Yes
          </v-btn>
          <v-btn
            class="mr-3"
            text
            @click="close"
          >
            No
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialog3"
      max-width="300"
    >
      <v-card>
        <v-card-title>
          Are you sure?

          <v-spacer />

          <v-icon
            aria-label="Close"
            @click="close"
          >
            mdi-close
          </v-icon>
        </v-card-title>

        <v-card-text class="pb-6 pt-12 text-center">
          <v-btn
            class="mr-3"
            text
            @click="close"
          >
            No
          </v-btn>

          <v-btn
            color="success"
            text
            @click="deleteitem"
          >
            Yes
          </v-btn>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-snackbar
      v-model="informSnackbar"
      :color="informColor"
      :timeout="timeout"
    >
      {{ informText }}

      <template #action="{ attrs }">
        <v-btn
          text
          v-bind="attrs"
          @click="informSnackbar = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <base-material-card-table
      icon="mdi-account-group"
      title="Customers"
      class="px-5 py-3"
      color="primary"
    >
      <v-tabs
        v-model="tabs"
        grow
        @change="tabChange"
      >
        <v-tab
          class="mr-3"
        >
          <v-icon class="mr-2">
            mdi-account-group
          </v-icon>
          Все
        </v-tab>
        <v-tab
          class="mr-3"
        >
          <v-icon class="mr-2">
            mdi-account-minus
          </v-icon>
          Должники
        </v-tab>
        <v-tab>
          <v-icon class="mr-2">
            mdi-account-plus
          </v-icon>
          Активные
        </v-tab>
        <v-tab>
          <v-icon class="mr-2">
            mdi-account-star
          </v-icon>
          Новые
        </v-tab>
      </v-tabs>

      <v-tabs-items
        v-model="tabs"
        class="transparent"
      >
        <v-tab-item
          v-for="n in 4"
          :key="n"
        >
          <v-data-table
            loading
            :headers="headers"
            :items="customers"
            sort-by="id"
            class="elevation-1"
            :expanded="expanded"
            :single-expand="singleExpand"
            :search="search"
            item-key="id"
            show-expand
            calculate-widths
            fixed-header
            @item-expanded="loadDetails"
          >
            <v-progress-linear
              v-show="progressBar"
              slot="progress"
              color="blue"
              indeterminate
            />
            <template #top>
              <v-toolbar
                flat
              >
                <v-text-field
                  v-model="search"
                  append-icon="mdi-magnify"
                  label="Search"
                  single-line
                  hide-details
                />
                <v-spacer />
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  :to="{ path: `/pages/customer/0`}"
                >
                  New Customer
                </v-btn>
              </v-toolbar>
            </template>
            <template #[`item.balance`]="{ item }">
              <v-list-item two-line>
                <v-list-item-content>
                  <v-list-item-title>
                    <router-link
                      v-slot="{ navigate }"
                      :to="{ path: `/tables/payments/${item.phone}`}"
                      tag="span"
                      custom
                    >
                      <v-chip
                        :color="getColor(item.balance)"
                        dark
                        label
                        :style="{ cursor: 'pointer', font: 'bold' }"
                        @click="navigate"
                      >
                        {{ item.balance }} €
                      </v-chip>
                    </router-link>
                  </v-list-item-title>
                  <v-list-item-subtitle>Credit: {{ item.credit }} €</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </template>
            <template #[`item.phone`]="{ item }">
              <v-list-item two-line>
                <v-list-item-content>
                  <v-list-item-title>{{ item.phone }}</v-list-item-title>
                  <v-list-item-subtitle>{{ item.email }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </template>
            <template #[`item.switch`]="{ item }">
              <v-switch
                v-model="item.status"
                dense
                false-value="0"
                true-value="1"
                :value="item.status"
                @change="setCustomerOption(item.id, 'status', item.status)"
              />
            </template>
            <template #[`item.charge`]="{ item }">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-subtitle>
                    <v-tooltip bottom>
                      <template #activator="{ on, attrs }">
                        <v-icon
                          v-bind="attrs"
                          v-on="on"
                          @click="setBalance(item,1)"
                        >
                          mdi-plus
                        </v-icon>
                      </template>
                      <span>Увеличить баланс</span>
                    </v-tooltip>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle>
                    <v-tooltip bottom>
                      <template #activator="{ on, attrs }">
                        <v-icon
                          v-bind="attrs"
                          v-on="on"
                          @click="setBalance(item,0)"
                        >
                          mdi-minus
                        </v-icon>
                      </template>
                      <span>Уменьшить баланс</span>
                    </v-tooltip>
                  </v-list-item-subtitle>
                  <v-list-item-subtitle>
                    <v-tooltip bottom>
                      <template #activator="{ on, attrs }">
                        <v-icon
                          v-bind="attrs"
                          v-on="on"
                          @click="transBalance(item)"
                        >
                          mdi-cached
                        </v-icon>
                      </template>
                      <span>Перенести на другой номер</span>
                    </v-tooltip>
                  </v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </template>
            <template #[`item.actions`]="{ item }">
              <!-- <v-icon
                v-if="item.status == 1"
                small
                class="mr-2"
                color="red"
                @click="informSnackbar = true"
              >
                mdi-stop
              </v-icon>
              <v-icon
                v-else
                small
                class="mr-2"
                color="green"
                @click="informSnackbar = true"
              >
                mdi-play
              </v-icon> -->
              <v-icon
                @click="editItem(item)"
              >
                mdi-pencil
              </v-icon>
              <v-icon
                small
                @click="informSnackbar = true"
              >
                mdi-message
              </v-icon>
              <v-icon
                small
                class="mr-4"
                @click="informSnackbar = true"
              >
                mdi-email
              </v-icon>
              <v-icon
                small
                color="error"
                @click="deleteItem(item)"
              >
                mdi-delete
              </v-icon>
            </template>
            <template #expanded-item="{ item }">
              <td :colspan="headers.length">
                <v-container>
                  <v-row
                    dense
                    justify="center"
                  >
                    <v-col
                      sm="1"
                      md="1"
                    >
                      <v-text-field
                        v-model="item.credit"
                        label="Credit"
                        dense
                        outlined
                        value="item.credit"
                        @change="setCustomerOption(item.id,'credit',item.credit)"
                      />
                    </v-col>
                    <v-col
                      sm="3"
                      md="3"
                    >
                      <v-select
                        v-model="item.tariff"
                        label="Tariff"
                        dense
                        outlined
                        :items="tariffs"
                        @change="changeTariff"
                        @focus="[editedIndex = item.id]"
                      />
                    </v-col>
                    <v-col>
                      <v-text-field
                        v-model="item.description"
                        label="Description"
                        dense
                        outlined
                        value="item.description"
                        @change="setCustomerOption(item.id,'description',item.description)"
                      />
                    </v-col>
                    <v-col>
                      <v-menu
                        top
                        offset-y
                      >
                        <template #activator="{ on, attrs }">
                          <v-btn
                            color="primary"
                            dark
                            outlined
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>
                              mdi-android-messages
                            </v-icon>
                          </v-btn>
                        </template>

                        <v-list>
                          <v-list-item>
                            <v-list-item-title>
                              <v-btn
                                class="mx-2"
                                dark
                                small
                                outlined
                                color="primary"
                                :href="item.Facebook"
                                target="_blank"
                              >
                                <v-icon dark>
                                  mdi-facebook
                                </v-icon>
                              </v-btn>
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item>
                            <v-list-item-title>
                              <v-btn
                                class="mx-2"
                                dark
                                small
                                outlined
                                color="primary"
                                :href="`https://wa.me/382${item.phone}`"
                                target="_blank"
                              >
                                <v-icon dark>
                                  mdi-whatsapp
                                </v-icon>
                              </v-btn>
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item>
                            <v-list-item-title>
                              <v-btn
                                class="mx-2"
                                dark
                                small
                                outlined
                                color="primary"
                                :href="`viber://chat?number=%2B382${item.phone}`"
                                target="_blank"
                              >
                                V
                              </v-btn>
                            </v-list-item-title>
                          </v-list-item>
                          <v-list-item>
                            <v-list-item-title>
                              <v-btn
                                class="mx-2"
                                dark
                                small
                                outlined
                                color="primary"
                                :href="`mailto:${item.email}`"
                              >
                                <v-icon dark>
                                  mdi-at
                                </v-icon>
                              </v-btn>
                            </v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>

                      <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        :to="{ path: `/pages/customer/${item.id}`}"
                      >
                        <v-icon dark>
                          mdi-format-list-bulleted-square
                        </v-icon>
                      </v-btn>
                    </v-col>
                  </v-row>
                  <v-spacer />
                  <v-row
                    justify="center"
                    dense
                  >
                    <v-col>
                      <v-data-table
                        dense
                        :headers="headersDetail"
                        :items="details"
                        item-key="row_num"
                        class="elevation-1"
                      >
                        <template #top>
                          <v-toolbar
                            flat
                          >
                            <!-- date -->
                            <v-menu
                              v-model="menu2"
                              :close-on-content-click="false"
                              :nudge-right="40"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template #activator="{ on, attrs }">
                                <v-text-field
                                  v-model="date"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  v-bind="attrs"
                                  v-on="on"
                                />
                              </template>
                              <v-date-picker
                                v-model="date"
                                no-title
                                scrollable
                                color="primary"
                                :events="arrayEvents"
                                event-color="primary"
                                @input="selectHysDate"
                              />
                            </v-menu>
                            <!-- date -->
                            <v-spacer />
                            <p class="font-weight-bold">
                              {{ hysBalance }} €
                            </p>
                          </v-toolbar>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </template>
            <!-- <v-btn
              color="primary"
              @click="getCustomers"
            >
              Reset
            </v-btn> -->
          </v-data-table>
        </v-tab-item>
      </v-tabs-items>
    </base-material-card-table>
  </v-container>
</template>
<script>
  import axios from 'axios'
  export default {
    data: () => ({
      dialogChangeTariff: false,
      dialogChangeBalance: false,
      dialogTransBalance: false,
      menu2: false,
      newTariffId: -1,
      dialog3: false,
      progressBar: true,
      informSnackbar: false,
      timeout: 3000,
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      dialog: false,
      expanded: [],
      search: '',
      singleExpand: true,
      customers: [],
      tariffs: [],
      arrayEvents: [],
      hysBalance: 0,
      details: [],
      date: new Date().toISOString().substr(0, 10),
      deleteItemIndex: -1,
      editedIndex: -1,
      selected: '',
      editedItem: {
        name: '',
        balance: 0,
        email: 0,
        phone: 0,
        route: 0,
        place: '',
        description: '',
        toName: '',
        toPhone: 0,
        toBalance: 0,
        toAmount: 0,
      },
      defaultItem: {
        id: 0,
        credit: 0,
        name: '',
        email: '',
        phone: 0,
        status: 0,
        balance: 0.00,
        place: '',
        description: '',
      },
      tabs: 0,
      places: [],
      // places: ['Терминал', 'Наличные', 'Безналичный расчет', 'Альфа-банк', 'Тинькофф', 'CKB', 'Сбербанк', 'Почта'],
      rules: {
        name: [val => (val || '').length > 0 || 'Это обязательное поле'],
        digits: [val => Number.isInteger(parseInt(val * 100)) || 'Должно быть ЧИСЛО!'],
        number: [val => Number.isInteger(parseInt(val)) || 'Должно быть целое число!'],
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
      },
      formTitleChangeBalance () {
        return this.editedItem.route === 0 ? 'Уменьшение' : 'Добавление'
      },
      headers () {
        return [
          { text: 'Имя', align: 'start', value: 'name' },
          { text: 'Контакт', align: 'center', value: 'phone', width: 10, visible: false },
          { text: 'Статус', align: 'center', value: 'switch', width: 10, sortable: false, filterable: false },
          { text: 'Баланс', align: 'center', value: 'balance', width: 10, filter: this.balanceFilter },
          { value: 'charge', align: 'start', width: 10, sortable: false, filterable: false },
        ]
      },
      headersDetail () {
        return [
          { text: 'Дата', value: 'datetime' },
          { text: 'Оплата', value: 'debt', width: 120, filterable: false },
          { text: 'Расход', value: 'ccredit', width: 120, filterable: false },
          { text: 'Баланс', value: 'cb', width: 120, filterable: false },
        ]
      },
      formIsValid () {
        return this.editedItem.place && this.editedItem.description && Number.isInteger(this.editedItem.balance * 100)
      },
      formTransIsValid () {
        return this.editedItem.toPhone && this.editedItem.description && Number.isInteger(this.editedItem.toAmount * 100) && this.editedItem.toName
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      customers () {
        this.progressBar = false
      },
    },

    created () {
      // this.getCustomers()
      this.getTariffs()
      this.getPayMethods()
    },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/customers')
        .then(response => {
          this.customers = response.data.map((item) => {
            return {
              details: [],
              ...item,
            }
          })
        })
    },
    methods: {
      calcNewBalance () {
        if (Number.isInteger(this.editedItem.toAmount * 100)) {
          this.editedItem.balance = Math.round(this.editedItem.balance * 100 - this.editedItem.toAmount * 100) / 100
          this.editedItem.toBalance = Math.round(this.editedItem.toBalance * 100 + this.editedItem.toAmount * 100) / 100
        }
      },
      searchByPhone () {
        if (this.editedItem.toPhone.startsWith('0')) {
          this.editedItem.toPhone = this.editedItem.toPhone.substring(1)
        }
        if (this.editedItem.toPhone.length === 8) {
          const customer = this.customers.find(({ phone }) => phone === this.editedItem.toPhone)
          if (customer === undefined) {
            this.msgError('Абонент не найден')
          } else {
            this.editedItem.toName = customer.name
            this.editedItem.toBalance = customer.balance
            this.editedItem.toAmount = 0
          }
        } else {
          this.editedItem.toName = ''
          this.editedItem.toAmount = 0
        }
      },
      getColor (balance) {
        if (balance < 0) return 'red'
        else if (balance > 0) return 'green'
        else return 'orange'
      },
      getStatus (state) {
        if (state < 1) return true
        else return false
      },
      selectHysDate () {
        axios.get('https://admin.montelcompany.me/api/getHysBalance?id=' + this.selected + '&date=' + this.date)
          .then(response => {
            this.hysBalance = response.data
          })
        axios.get('https://admin.montelcompany.me/api/getBills?number=' + this.selected + '&date=' + this.date)
          .then(response => {
            this.details = response.data
          })
          .catch(function (error) {
            this.msgError(error)
            console.log(error)
          })
        this.menu2 = false
      },
      changeStatus (item) {
        this.informColor = 'warning'
        this.informText = 'UNDER CONSTRUCTION'
        this.informSnackbar = true
      },
      getTariffs: function (app = this) {
        axios.get('https://admin.montelcompany.me/api/tariffs')
          .then(response => {
            this.tariffs = response.data.map(item => {
              return { text: item.Name, value: item.id }
            })
          })
      },
      getPayMethods: function (app = this) {
        axios.get('https://admin.montelcompany.me/api/getPayMethods')
          .then(response => {
            this.places = response.data.map(item => {
              return { text: item.name }
            })
          })
      },
      getTariffName () {
        return this.tariffs[this.newTariffId].text
      },
      tabChange: async function (obj, app = this) {
        app.progressBar = true
        var filter = 'new'
        if (obj !== 3) { filter = 'balance&val=' + obj }
        await axios
          .get('https://admin.montelcompany.me/api/customers?filter=' + filter)
          .then(function (response) {
            app.customers = response.data
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      loadDetails ({ item }) {
        axios.get('https://admin.montelcompany.me/api/getBills?number=' + item.phone)
          .then(response => {
            this.details = response.data
            this.arrayEvents = this.details.map(item => new Date(item.datetime).toISOString().substr(0, 10))
          })
          .catch(function (error) {
            this.msgError(error)
            console.log(error)
          })
        this.selected = item.phone
        this.hysBalance = item.balance
      },
      editItem (item) {
        this.editedIndex = this.customers.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },
      deleteItem (item) {
        this.deleteItemIndex = this.customers.indexOf(item)
        this.dialog3 = true
      },
      changeTariff (selectObj) {
        this.newTariffId = selectObj
        this.dialogChangeTariff = true
      },
      setBalance (item, route) {
        this.editedIndex = this.customers.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.editedItem.route = route
        this.editedItem.balance = 0
        this.dialogChangeBalance = true
      },
      transBalance (item) {
        this.editedIndex = this.customers.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogTransBalance = true
      },
      setCustomerOption (userId, option, value) {
        axios.post('https://admin.montelcompany.me/api/setCustomerOption', {
          id: userId,
          option: option,
          value: value,
        })
        this.msgSuccess('Option ' + option + ' has ben changed')
      },
      confirmChangeTariff () {
        axios.post('https://admin.montelcompany.me/api/newTariff', {
          id: this.editedIndex,
          newTariffId: this.newTariffId,
        })
        this.dialogChangeTariff = false
        this.newTariffId = -1
        this.editedIndex = -1
        this.msgSuccess('Tariff has ben changed')
      },
      changeBalance () {
        if (this.editedItem.route === 0) {
          this.editedItem.balance = this.editedItem.balance * -1
          this.editedItem.place = 'Корректировка (списание)'
        }
        axios.post('https://admin.montelcompany.me/api/chargeCustom', {
          number: this.editedItem.phone,
          place: this.editedItem.place,
          amount: this.editedItem.balance,
          provider: 'montel',
          description: this.editedItem.description,
        })
        this.msgSuccess('Успешно изменен баланс ' + this.editedItem.phone + ' на ' + this.editedItem.balance + '€')
        this.customers[this.editedIndex].balance = (this.editedItem.balance * 100 + this.customers[this.editedIndex].balance * 100) / 100
        this.close()
      },
      transBalanceSend () {
        axios.post('https://admin.montelcompany.me/api/chargeCustom', {
          number: this.editedItem.phone,
          place: 'Transfer',
          amount: this.editedItem.toAmount * -1,
          provider: 'montel',
          description: this.editedItem.description,
        })
        axios.post('https://admin.montelcompany.me/api/chargeCustom', {
          number: this.editedItem.toPhone,
          place: 'Transfer',
          amount: this.editedItem.toAmount,
          provider: 'montel',
          description: this.editedItem.description,
        })
        this.msgSuccess('Успешно перенесено ' + this.editedItem.toAmount + '€ c ' + this.editedItem.phone + ' на ' + this.editedItem.toPhone)
        this.customers[this.editedIndex].balance = (this.customers[this.editedIndex].balance * 100 - this.editedItem.toAmount * 100) / 100
        this.close()
      },
      close () {
        this.dialog = false
        this.dialog3 = false
        this.dialogChangeTariff = false
        this.dialogChangeBalance = false
        this.dialogTransBalance = false
        this.$nextTick(() => {
          this.deleteItemIndex = -1
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
          this.newTariffId = -1
        })
      },
      underConstructionMsg () {
        this.informColor = 'warning'
        this.informText = 'UNDER CONSTRUCTION'
        this.informSnackbar = true
      },
      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.customers[this.editedIndex], this.editedItem)
        } else {
          this.customers.push(this.editedItem)
        }
        this.close()
      },
      msgSuccess (msg) {
        this.informColor = 'success'
        this.informText = msg
        this.informSnackbar = true
      },
      msgError (msg) {
        this.informColor = 'red'
        this.informText = msg
        this.informSnackbar = true
      },
      deleteitem () {
        this.customers.splice(this.deleteItemIndex, 1)
        this.close()
      },
    },
  }
</script>
