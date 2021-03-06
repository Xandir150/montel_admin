<template>
  <v-container
    id="payments"
    fluid
    tag="section"
  >
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
      icon="mdi-cash-multiple"
      :title="`Рассчёты ${getTitle}`"
      class="px-5 py-3"
      color="primary"
    >
      <v-data-table
        loading
        :headers="headers"
        :items="bills"
        sort-by="datetime"
        sort-desc
        class="elevation-1"
        :search="search"
        :expanded="expanded"
        :single-expand="singleExpand"
        show-expand
        item-key="row_num"
        calculate-widths
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
              label="Поиск"
              single-line
              hide-details
            />
            <v-spacer />
            <!-- date -->
            <v-menu
              ref="menu"
              v-model="menu"
              :close-on-content-click="false"
              :return-value.sync="dates"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template #activator="{ on, attrs }">
                <v-text-field
                  v-model="dateRangeText"
                  label="Date range"
                  v-bind="attrs"
                  v-on="on"
                />
              </template>
              <v-date-picker
                v-model="dates"
                no-title
                scrollable
                range
              >
                <v-btn
                  text
                  color="primary"
                  @click="menu = false"
                >
                  Cancel
                </v-btn>
                <v-btn
                  text
                  color="primary"
                  @click="$refs.menu.save(dates)"
                >
                  OK
                </v-btn>
              </v-date-picker>
            </v-menu>
            <!-- date -->
            <v-spacer />
          </v-toolbar>
        </template>
        <template #[`item.name`]="{ item }">
          <p>
            {{ item.name }}
          </p>
          <p>
            {{ getDateTime(item.datetime) }}
          </p>
        </template>
        <template #[`item.debt`]="{ item }">
          <p class="font-weight-bold">
            {{ item.debt }}
          </p>
        </template>
        <template #[`item.ccredit`]="{ item }">
          <p class="font-weight-bold">
            {{ item.ccredit }}
          </p>
        </template>
        <template #[`item.balance`]="{ item }">
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title>
                <router-link
                  :to="{ path: `/pages/customer/${item.id}`}"
                  tag="span"
                  exact
                >
                  <v-chip
                    :color="getColor(item.balance)"
                    dark
                    label
                    :style="{ cursor: 'pointer'}"
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
        <template #[`item.info`]="{ item }">
          <v-btn
            class="mb-4"
            dark
            small
            outlined
            color="primary"
            :to="{ path: `/pages/customer/${item.id}`}"
          >
            <v-icon dark>
              mdi-format-list-bulleted-square
            </v-icon>
          </v-btn>
        </template>
        <v-btn
          color="primary"
          @click="getPayments"
        >
          Reset
        </v-btn>
        <template #expanded-item="{ item }">
          <td :colspan="headers.length">
            <v-container>
              <v-row no-gutters>
                <v-simple-table
                  v-if="item.ccredit > 0"
                  dense
                >
                  <template #default>
                    <thead>
                      <tr>
                        <th class="text-center">
                          Doc
                        </th>
                        <th class="text-center">
                          Type
                        </th>
                        <th class="text-center">
                          MG
                        </th>
                        <th class="text-center">
                          Call 68
                        </th>
                        <th class="text-center">
                          Call CG
                        </th>
                        <th class="text-center">
                          Call Fix
                        </th>
                        <th class="text-center">
                          SMS CG
                        </th>
                        <th class="text-center">
                          SMS abroad
                        </th>
                        <th class="text-center">
                          GPRS
                        </th>
                        <th class="text-center">
                          Call spec
                        </th>
                        <th class="text-center">
                          Call abroad
                        </th>
                        <th class="text-center">
                          Roaming
                        </th>
                        <th class="text-center">
                          Services
                        </th>
                        <th class="text-center">
                          MMS
                        </th>
                        <th class="text-center">
                          Over limit
                        </th>
                        <th class="text-center">
                          Discount
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center">
                          {{ item.details.doc_num }}
                        </td>
                        <td class="text-center">
                          {{ item.details.service }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.amount) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.calls_local) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.calls_other) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.calls_landline) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.sms_national) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.sms_international) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.gprs) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.calls_special) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.call_international) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.roaming) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.mms) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.addational_service) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.over_limit) }}
                        </td>
                        <td class="text-center">
                          {{ addPdv(item.details.discount) }}
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
                <v-simple-table
                  v-else
                  dense
                >
                  <template #default>
                    <thead>
                      <tr>
                        <th class="text-center">
                          Место оплаты
                        </th>
                        <th class="text-center">
                          Дата время
                        </th>
                        <th class="text-center">
                          Комментарий
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center">
                          {{ item.place }}
                        </td>
                        <td class="text-center">
                          {{ getDateTime(item.datetime) }}
                        </td>
                        <td class="text-center">
                          {{ item.pdesc }}
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </v-row>
            </v-container>
          </td>
        </template>
      </v-data-table>
    </base-material-card-table>
  </v-container>
</template>
<script>
  import axios from 'axios'
  export default {
    name: 'Payments',
    props: {
      customerid: {
        type: String,
        required: true,
      },
    },
    data: () => ({
      dates: [new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10)],
      menu: false,
      dialog: false,
      progressBar: true,
      informSnackbar: false,
      timeout: 3000,
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      search: '',
      bills: [],
      expanded: [],
      singleExpand: true,
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
      },
      headers () {
        return [
          { text: 'Имя', align: 'start', sortable: false, value: 'name' },
          { text: 'Контакт', value: 'phone', sortable: false },
          // { text: 'Place', align: 'center', value: 'place', sortable: false, width: 120 },
          { text: 'Дебет', align: 'center', value: 'debt', width: 100, filterable: false },
          { text: 'Кредит', align: 'center', value: 'ccredit', width: 100, filterable: false },
          { text: 'Баланс', align: 'center', value: 'balance', width: 10, sortable: false, filter: this.balanceFilter },
          { value: 'info', width: 10, sortable: false, filterable: false },
        ]
      },
      dateRangeText () {
        return this.dates.join(' ~ ')
      },
      getTitle () {
        if (this.customerid > 0) {
          if (this.bills.length > 0) return this.bills[0].name
          else return this.customerid
        } else return 'все'
      },
    },

    watch: {
      bills () {
        this.progressBar = false
      },
      dialog (val) {
        val || this.close()
      },
    },

    created () {
      this.getPayments()
    },

    methods: {
      addPdv (val) {
        return Math.round(parseFloat(val) * 1.21, 4)
      },
      getColor (balance) {
        if (balance < 0) return 'red'
        else if (balance > 0) return 'green'
        else return 'orange'
      },
      getDateTime (datetime) {
        datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
      },
      getPayments: function (app = this) {
        if (this.customerid > 0) {
          axios
            .get('https://admin.montelcompany.me/api/getBills?number=' + this.customerid)
            .then(response => {
              this.bills = response.data.map((item) => {
                return {
                  details: {},
                  ...item,
                }
              })
            })
            .catch(function (error) {
              console.log(error)
            })
        } else {
          axios
            .get('https://admin.montelcompany.me/api/getBills')
            .then(response => {
              this.bills = response.data.map((item) => {
                return {
                  details: {},
                  ...item,
                }
              })
            })
            .catch(function (error) {
              console.log(error)
            })
        }
      },
      loadDetails ({ item }) {
        axios.get('https://admin.montelcompany.me/api/invoices?number=' + item.number + '&doc=' + item.place + '&service=' + item.service)
          .then(response => {
            item.details = response.data
          })
          .catch(function (error) {
            this.informColor = 'error'
            this.informText = error
            this.informSnackbar = true
            console.log(error)
          })
      },
      close () {
        this.dialog = false
      },
    },
  }
</script>
