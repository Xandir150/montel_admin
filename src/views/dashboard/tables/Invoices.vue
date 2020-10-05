<template>
  <v-container
    id="invoices"
    fluid
    tag="section"
  >
    <v-snackbar
      v-model="underconstuction"
      color="warning"
      :timeout="timeout"
    >
      UNDER CONSTRUCTION

      <template #action="{ attrs }">
        <v-btn
          color="blue"
          text
          v-bind="attrs"
          @click="underconstuction = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <base-material-card-table
      icon="mdi-cash-multiple"
      :title="`Invoces ${getTitle}`"
      class="px-5 py-3"
      color="primary"
    >
      <v-data-table
        loading
        :headers="headers"
        :items="invoices"
        sort-by="datetime"
        sort-desc
        class="elevation-1"
        :search="search"
        :expanded="expanded"
        :single-expand="singleExpand"
        item-key="row_num"
        calculate-widths
        show-expand
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
          </v-toolbar>
        </template>
        <template #[`item.amount`]="{ item }">
          <p class="font-weight-bold">
            {{ parseFloat(item.amount) }} €
          </p>
        </template>
        <template #expanded-item="{ item }">
          <td :colspan="headers.length">
            <v-container>
              <v-row no-gutters>
                <v-simple-table dense>
                  <template #default>
                    <thead>
                      <tr>
                        <th class="text-left">
                          calls_local
                        </th>
                        <th class="text-left">
                          calls_other
                        </th>
                        <th class="text-left">
                          calls_landline
                        </th>
                        <th class="text-left">
                          sms_national
                        </th>
                        <th class="text-left">
                          sms_international
                        </th>
                        <th class="text-left">
                          gprs
                        </th>
                        <th class="text-left">
                          calls_special
                        </th>
                        <th class="text-left">
                          call_international
                        </th>
                        <th class="text-left">
                          roaming
                        </th>
                        <th class="text-left">
                          addational_service
                        </th>
                        <th class="text-left">
                          mms
                        </th>
                        <th class="text-left">
                          over_limit
                        </th>
                        <th class="text-left">
                          discount
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>{{ parseFloat(item.calls_local) }}</td>
                        <td>{{ parseFloat(item.calls_other) }}</td>
                        <td>{{ parseFloat(item.calls_landline) }}</td>
                        <td>{{ parseFloat(item.sms_national) }}</td>
                        <td>{{ parseFloat(item.sms_international) }}</td>
                        <td>{{ parseFloat(item.gprs) }}</td>
                        <td>{{ parseFloat(item.calls_special) }}</td>
                        <td>{{ parseFloat(item.call_international) }}</td>
                        <td>{{ parseFloat(item.roaming) }}</td>
                        <td>{{ parseFloat(item.mms) }}</td>
                        <td>{{ parseFloat(item.over_limit) }}</td>
                        <td>{{ parseFloat(item.discount) }}</td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table>
                <!-- <v-col order="last">
                  <v-data-table
                    dense
                    :headers="paymentHeaders"
                    :items="item.payments"
                    item-key="name"
                    class="elevation-1"
                  >
                    <template #[`item.amount`]="{ item }">
                      <p :style="{ color: getCollectColor(item.number)}">
                        {{ item.amount/100 }} €
                      </p>
                    </template>
                  </v-data-table>
                </v-col> -->
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
      invoiceid: {
        type: String,
        required: false,
      },
    },
    data: () => ({
      dates: [new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10)],
      menu: false,
      progressBar: true,
      underconstuction: false,
      timeout: 3000,
      search: '',
      invoices: [],
      expanded: [],
      singleExpand: true,
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
      },
      headers () {
        return [
          { text: 'Num', align: 'start', value: 'doc_num' },
          { text: 'Amount', value: 'amount' },
          { text: 'Number', value: 'number' },
          { text: 'provider', value: 'provider' },
        ]
      },
      dateRangeText () {
        return this.dates.join(' ~ ')
      },
      getTitle () {
        if (this.invoiceid > 0) {
          if (this.bills.length > 0) return this.bills[0].name
          else return this.invoiceid
        } else return 'all'
      },
    },

    watch: {
      invoices () {
        this.progressBar = false
      },
    },
    // mounted () {
    //   axios
    //     .get('https://admin.montelcompany.me/api/invoices')
    //     .then(function (response) {
    //       this.invoices = response.data
    //     })
    //     .catch(function (error) {
    //       console.log(error)
    //     })
    // },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/invoices')
        .then(response => {
          this.invoices = response.data.map((item) => {
            return {
              details: {
                calls_local: item.calls_local,
                calls_other: item.calls_other,
                calls_landline: item.calls_landline,
                sms_national: item.sms_national,
                sms_international: item.sms_international,
                gprs: item.gprs,
                calls_special: item.calls_special,
                call_international: item.call_international,
                roaming: item.roaming,
                addational_service: item.addational_service,
                mms: item.mms,
                over_limit: item.over_limit,
                discount: item.discount,
              },
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
      getDateTime (datetime) {
        datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
      },
    },
  }
</script>
