<template>
  <v-container
    id="invoices"
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
      icon="mdi-receipt"
      :title="`Счета ${getTitle}`"
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
            <!-- <v-menu
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
            </v-menu> -->
            <!-- date -->
            <v-spacer />
            <v-dialog
              v-model="dialog"
              max-width="500px"
            >
              <template #activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  v-bind="attrs"
                  v-on="on"
                >
                  Загрузить счёт
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="headline">Загрузка счёта оператора M:TEL</span>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-file-input
                        v-model="files"
                        placeholder="Выберите файл"
                        truncate-length="50"
                        accept=".xlsx"
                        label="Файл счёта для загрузки"
                        prepend-icon="mdi-microsoft-excel"
                      />
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
                    Отмена
                  </v-btn>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click="submitFiles"
                  >
                    GO!
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template #[`item.amount`]="{ item }">
          {{ Math.round(parseFloat(item.amount) * 100) / 100 }}
        </template>
        <template #[`item.discount`]="{ item }">
          {{ Math.round(parseFloat(item.discount) * 100) / 100 }}
        </template>
        <v-btn
          color="primary"
          @click="getData"
        >
          Reset
        </v-btn>
        <template #expanded-item="{ item }">
          <td :colspan="headers.length">
            <v-container>
              <v-row no-gutters>
                <v-simple-table
                  dense
                >
                  <template #default>
                    <thead>
                      <tr>
                        <th class="text-center">
                          Number
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
                        <th class="text-center">
                          Balance
                        </th>
                        <th class="text-center">
                          Total
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="row in item.details"
                        :key="row.name"
                      >
                        <td class="text-center">
                          <router-link
                            v-slot="{ navigate }"
                            :to="{ path: `/tables/payments/${row.number}`}"
                            tag="span"
                            custom
                          >
                            <span
                              role="link"
                              :style="{ cursor: 'pointer', font: 'bold' }"
                              @click="navigate"
                              @keypress.enter="navigate"
                            > {{ row.number }}</span>
                          </router-link>
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.amount) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.calls_local) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.calls_other) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.calls_landline) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.sms_national) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.sms_international) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.gprs) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.calls_special) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.call_international) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.roaming) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.mms) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.addational_service) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.over_limit) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.discount) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.cb) * 100) / 100 }}
                        </td>
                        <td class="text-center">
                          {{ Math.round(parseFloat(row.tAmount) * 100) / 100 }}
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
    name: 'Invoices',
    props: {
      invoiceid: {
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
      files: [],
      singleExpand: true,
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
      },
      headers () {
        return [
          { text: 'Дата', align: 'start', width: 120, sortable: false, value: 'date' },
          { text: 'Номер', value: 'doc_num', sortable: false },
          { text: 'Провайдер', value: 'provider', sortable: false },
          { text: 'Абонплата', value: 'amount', width: 120, filterable: false },
          { text: 'Расходы', value: 'overfee', width: 120, filterable: false },
          { text: 'Скидка', value: 'discount', width: 120, sortable: false, filterable: false },
          { text: 'С клиентов', value: 'tamount', width: 120, sortable: false, filterable: false },
          { text: 'Балансы клиентов', value: 'client_balances', sortable: false, filterable: false },
          { text: 'Профит', value: 'revenue', sortable: false, filterable: false },
        ]
      },
      dateRangeText () {
        return this.dates.join(' ~ ')
      },
      getTitle () {
        if (this.invoiceid > 0) {
          if (this.bills.length > 0) return this.bills[0].name
          else return this.invoiceid
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
      this.getData()
    },

    methods: {
      addPdv (val) {
        return Math.round(parseFloat(val) * 1.21 * 10000) / 10000
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
      getData: function (app = this) {
        // s когда бдем получать конкретный счёт или по фильтру
        if (this.invoiceid > 0) {
          axios
            .get('https://admin.montelcompany.me/api/getBills?number=' + this.invoiceid)
            .then(response => {
              this.bills = response.data.map((item) => {
                return {
                  details: [],
                  ...item,
                }
              })
            })
            .catch(function (error) {
              console.log(error)
            })
        } else {
          // s
          axios
            .get('https://admin.montelcompany.me/api/getInvoicesList')
            .then(response => {
              this.bills = response.data.map((item) => {
                return {
                  details: [],
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
        axios.get('https://admin.montelcompany.me/api/invoices?&doc=' + item.doc_num + '&service=bill')
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
      submitFiles () {
        if (this.files) {
          const formData = new FormData()
          formData.append('files', this.files, this.files.name)
          // files
          // for (const file of this.files) {
          //   formData.append('files', file, file.name)
          //   console.log(file.name)
          // }
          // // additional data
          // formData.append('test', 'foo bar')
          // this.dialog = false
          axios
            .post('https://admin.montelcompany.me/api/uploadInvoice', formData)
            .then(response => {
              if (response.status === 200) {
                this.close()
                this.informColor = 'success'
                this.informText = 'Успешно загружено ' + response.data + ' строк.'
                this.informSnackbar = true
              }
            })
            .catch(error => {
              this.dialog = false
              this.informColor = 'error'
              this.informText = error
              this.informSnackbar = true
              console.log({ error })
            })
        } else {
          console.log('there are no files.')
          this.dialog = false
          this.informColor = 'error'
          this.informText = 'there are no files.'
          this.informSnackbar = true
        }
      },
      close () {
        this.files = null
        this.dialog = false
      },
    },
  }
</script>
