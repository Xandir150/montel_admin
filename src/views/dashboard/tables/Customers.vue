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
            label="Сумма"
            suffix="€"
          />
          <v-text-field
            v-model="editedItem.description"
            :rules="[rules.required, rules.counter]"
            required
            label="Комментарий"
            counter
            maxlength="64"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="success"
            text
            @click="underConstructionMsg"
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
          All
        </v-tab>
        <v-tab
          class="mr-3"
        >
          <v-icon class="mr-2">
            mdi-account-minus
          </v-icon>
          Debtors
        </v-tab>
        <v-tab>
          <v-icon class="mr-2">
            mdi-account-plus
          </v-icon>
          Good
        </v-tab>
        <v-tab>
          <v-icon class="mr-2">
            mdi-account-star
          </v-icon>
          New
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
                <v-dialog
                  v-model="dialog"
                  max-width="500px"
                >
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      dark
                      class="mb-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      New Customer
                    </v-btn>
                  </template>
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>

                    <v-card-text>
                      <v-container>
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-text-field
                              v-model="editedItem.name"
                              label="Name"
                            />
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-text-field
                              v-model="editedItem.credit"
                              label="Credit"
                            />
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-text-field
                              v-model="editedItem.email"
                              label="Email"
                            />
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            md="4"
                          >
                            <v-text-field
                              v-model="editedItem.phone"
                              label="Phone"
                            />
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer />
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="close"
                      >
                        Cancel
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="save"
                      >
                        Save
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
            <template #[`item.balance`]="{ item }">
              <v-list-item two-line>
                <v-list-item-content>
                  <v-list-item-title>
                    <!-- <router-link
                      :to="{ path: '/pages/customer/:', query: { id: item.id }}"
                      tag="span"
                      exact
                      :style="{ cursor: 'pointer'}"
                    > -->
                    <router-link
                      :to="{ path: `/tables/payments/${item.phone}`}"
                      tag="span"
                      exact
                    >
                      <v-chip
                        :color="getColor(item.balance)"
                        dark
                        label
                        :style="{ cursor: 'pointer', font: 'bold' }"
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
                    justify="space-around"
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
                      <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        :href="item.Facebook"
                        target="_blank"
                      >
                        <v-icon dark>
                          mdi-facebook
                        </v-icon>
                      </v-btn>

                      <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        :href="`https://wa.me/382${item.phone}`"
                        target="_blank"
                      >
                        <v-icon dark>
                          mdi-whatsapp
                        </v-icon>
                      </v-btn>

                      <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        :href="`viber://chat?number=%2B382${item.phone}`"
                        target="_blank"
                      >
                        V
                      </v-btn>

                      <v-btn
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        :href="`mailto:${item.email}`"
                      >
                        <v-icon dark>
                          mdi-at
                        </v-icon>
                      </v-btn>
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
      deleteItemIndex: -1,
      editedIndex: -1,
      editedItem: {
        name: '',
        balance: 0,
        email: 0,
        phone: 0,
        route: 0,
        description: '',
      },
      defaultItem: {
        id: 0,
        credit: 0,
        name: '',
        email: '',
        phone: 0,
        status: 0,
        balance: 0.00,
      },
      tabs: 0,
      rules: {
        required: value => !!value || 'Required.',
        counter: value => value.length <= 64 || 'Max 64 characters',
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
          { text: 'Name', align: 'start', value: 'name' },
          { text: 'Contact', align: 'center', value: 'phone', width: 10, visible: false },
          { text: 'Online', align: 'center', value: 'switch', width: 10, sortable: false, filterable: false },
          { text: 'Balance', align: 'center', value: 'balance', width: 10, filter: this.balanceFilter },
          { value: 'charge', align: 'start', width: 10, sortable: false, filterable: false },
        ]
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
    },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/customers')
        .then(response => {
          this.customers = response.data.map((item) => {
            return {
              ...item,
            }
          })
        })
    },
    methods: {
      getColor (balance) {
        if (balance < 0) return 'red'
        else if (balance > 0) return 'green'
        else return 'orange'
      },
      getStatus (state) {
        if (state < 1) return true
        else return false
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
      getTariffName () {
        console.log(this.tariffs[this.newTariffId].text)
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
        this.editedItem = Object.assign({}, item)
        this.editedItem.route = route
        this.dialogChangeBalance = true
      },
      setCustomerOption (userId, option, value) {
        axios.post('https://admin.montelcompany.me/api/setCustomerOption', {
          id: userId,
          option: option,
          value: value,
        })
        this.informColor = 'success'
        this.informText = 'Option ' + option + ' has ben changed'
        this.informSnackbar = true
      },
      confirmChangeTariff () {
        axios.post('https://admin.montelcompany.me/api/newTariff', {
          id: this.editedIndex,
          newTariffId: this.newTariffId,
        })
        this.dialogChangeTariff = false
        this.newTariffId = -1
        this.editedIndex = -1
        this.informColor = 'success'
        this.informText = 'Tariff has ben changed'
        this.informSnackbar = true
      },
      close () {
        this.dialog = false
        this.dialog3 = false
        this.dialogChangeTariff = false
        this.dialogChangeBalance = false
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
      deleteitem () {
        this.customers.splice(this.deleteItemIndex, 1)
        this.close()
      },
    },
  }
</script>
