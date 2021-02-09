<template>
  <v-container
    id="reports"
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
      icon="mdi-file-chart"
      :title="`Отчёт ${getTitle}`"
      class="px-5 py-3"
      color="primary"
    >
    </base-material-card-table>
  </v-container>
</template>
<script>
  import axios from 'axios'
  export default {
    name: 'Reports',
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
        // if (this.customerid > 0) {
        //   if (this.bills.length > 0) return this.bills[0].name
        //   else return this.customerid
        // } else return 'все'
        return ''
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
