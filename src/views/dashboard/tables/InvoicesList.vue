<template>
  <v-container
    id="invoiceslist"
    fluid
    tag="section"
  >
    <v-snackbar
      v-model="informSnackbar"
      color="warning"
      :timeout="timeout"
    >
      UNDER CONSTRUCTION

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
      icon="mdi-content-paste"
      title="Invoices"
      class="px-5 py-3"
      color="primary"
    >
      <v-data-table
        loading
        :headers="headers"
        :items="terminals"
        sort-by="id"
        class="elevation-1"
        :expanded="expanded"
        :single-expand="singleExpand"
        :search="search"
        item-key="id"
        show-expand
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
          </v-toolbar>
        </template>
        <template #[`item.amount`]="{ item }">
          <p class="font-weight-bold">
            {{ item.amount/100 }} €
          </p>
        </template>
        <template #[`item.lastpay`]="{ item }">
          {{ getDateTime(item.lastpay) }}
        </template>
        <template #[`item.ping`]="{ item }">
          <v-chip
            :color="getColor(item)"
            dark
            label
            :style="{ cursor: 'pointer'}"
          >
            {{ getTime(item.ping) }}
          </v-chip>
        </template>
        <template #[`item.action`]="{ item }">
          <v-tooltip bottom>
            <template #activator="{ on, attrs }">
              <v-icon
                v-bind="attrs"
                v-on="on"
                @click="restart(item.id)"
              >
                mdi-restart
              </v-icon>
            </template>
            <span>Restart terminal</span>
          </v-tooltip>
        </template>
        <template #expanded-item="{ item }">
          <td :colspan="headers.length">
            <v-container>
              <v-row no-gutters>
                <v-col order="last">
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
                  <!-- <v-simple-table dense>
                    <template #default>
                      <thead>
                        <tr>
                          <th class="text-left">
                            Date
                          </th>
                          <th class="text-center">
                            Amount
                          </th>
                          <th class="text-end">
                            Number
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="payment in item.payments"
                          :key="payment.name"
                        >
                          <td>{{ payment.datetime }}</td>
                          <td>{{ payment.amount }}</td>
                          <td>{{ payment.number }}</td>
                        </tr>
                      </tbody>
                    </template>
                  </v-simple-table> -->
                </v-col>
              </v-row>
            </v-container>
          </td>
        </template>
        <!-- <v-btn
          color="primary"
          @click="getTerminals"
        >
          Reset
        </v-btn> -->
      </v-data-table>
      <div class="text-center pt-2">
        <v-text-field
          label="Total"
          outlined
          readonly
          :value="getSum()"
        />
      </div>
    </base-material-card-table>
  </v-container>
</template>
<script>
  import axios from 'axios'
  export default {
    name: 'Invoiceslist',
    data: () => ({
      dates: [new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10)],
      progressBar: true,
      informSnackbar: false,
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      timeout: 3000,
      dialog: false,
      expanded: [],
      search: '',
      singleExpand: true,
      terminals: [],
    }),

    computed: {
      headers () {
        return [
          { text: 'Name', align: 'start', value: 'id' },
          { text: 'Last Contact', align: 'center', value: 'ping', filterable: false },
          { text: 'Last Pay', align: 'center', value: 'lastpay', filterable: false },
          { text: 'Balance', align: 'end', value: 'amount', width: 100, filterable: false },
          { value: 'action', align: 'end', width: 10, sortable: false, filterable: false },
        ]
      },
      // pingHeaders () {
      //   return [
      //     { text: 'Pings', align: 'start', value: 'datetime' },
      //   ]
      // },
      paymentHeaders () {
        return [
          { text: 'Payments', align: 'start', value: 'datetime' },
          { text: 'Amount', align: 'center', value: 'amount' },
          { text: 'Number', align: 'end', value: 'number' },
        ]
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      terminals () {
        this.progressBar = false
      },
    },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/terminals')
        .then(response => {
          this.terminals = response.data.map((item) => {
            return {
              // pings: [],
              payments: [],
              ...item,
            }
          })
        })
    },
    methods: {
      getCollectColor (number) {
        if (number === '68829160') { return 'red' }
      },
      getColor (item) {
        var time = new Date(item.ping).getTime() - this.getTimeOffset() * 60 * 1000
        var diff = new Date().getTime() - time
        if (diff > 36000000) return 'red'
        else if (diff < 1200000) return 'green'
        else return 'orange'
      },
      loadDetails ({ item }) {
        axios.get('https://admin.montelcompany.me/api/terminals?dt=payments&id=' + item.id)
          .then(response => {
            item.payments = response.data
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      getTime (datetime) {
        datetime = new Date(new Date(datetime).getTime() - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleTimeString()
      },
      getDateTime (datetime) {
        datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
      },
      getSum () {
        var amount = 0
        this.terminals.forEach(element => {
          amount += parseInt(element.amount) / 100
        })
        return amount + ' €'
      },
      restart (id) {
        this.informColor = 'warning'
        this.informText = 'UNDER CONSTRUCTION'
        this.informSnackbar = true
      },
    },
  }
</script>
