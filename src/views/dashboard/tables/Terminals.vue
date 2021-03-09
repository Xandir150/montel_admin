<template>
  <v-container
    id="terminals"
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
    <v-row>
      <v-col>
        <base-material-card-table
          icon="mdi-desktop-classic"
          title="Terminals"
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
                <div class="text-left pt-2">
                  Total: {{ sumField('amount', terminals) / 100 }} €
                </div>
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
                  <v-row
                    no-gutters
                    dense
                  >
                    <v-col order="last">
                      <v-data-table
                        dense
                        :headers="paymentHeaders"
                        :items="payments"
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
                              {{ sumField('amount', payments) / 100 }} €
                            </p>
                          </v-toolbar>
                        </template>
                        <template #[`item.amount`]="{ item }">
                          <p :style="{ color: getCollectColor(item.number)}">
                            {{ item.amount/100 }} €
                          </p>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </template>
          </v-data-table>
        </base-material-card-table>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <base-material-card-table
          icon="mdi-credit-card"
          title="Other methods"
          class="px-5 py-3"
          color="primary"
        >
          <v-data-table
            loading
            :headers="headersNt"
            :items="noTerminals"
            sort-by="id"
            class="elevation-1"
            :expanded="expanded"
            :single-expand="singleExpand"
            :search="search"
            item-key="row_num"
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
                <div class="text-left pt-2">
                  Total: {{ sumField('amount', noTerminals) / 100 }} €
                </div>
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
                        :items="payments"
                        item-key="row_num"
                        class="elevation-1"
                      >
                      <template #top>
                          <v-toolbar
                            flat
                          >
                            <!-- date -->
                            <v-menu
                              v-model="menu3"
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
                              {{ sumField('amount', payments) / 100 }} €
                            </p>
                          </v-toolbar>
                        </template>
                        <template #[`item.amount`]="{ item }">
                          <p :style="{ color: getCollectColor(item.number)}">
                            {{ item.amount/100 }} €
                          </p>
                        </template>
                      </v-data-table>
                    </v-col>
                  </v-row>
                </v-container>
              </td>
            </template>
          </v-data-table>
        </base-material-card-table>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
  import axios from 'axios'
  export default {
    name: 'Terminals',
    data: () => ({
      dates: [new Date().toISOString().substr(0, 10), new Date().toISOString().substr(0, 10)],
      progressBar: true,
      informSnackbar: false,
      menu2: false,
      menu3: false,
      date: new Date().toISOString().substr(0, 10),
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      timeout: 3000,
      dialog: false,
      expanded: [],
      search: '',
      singleExpand: true,
      terminals: [],
      payments: [],
      noTerminals: [],
      arrayEvents: [],
      hysBalance: 0,
      selected: '',
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
      headersNt () {
        return [
          { text: 'Name', align: 'start', value: 'id' },
          { text: 'Last Pay', align: 'center', value: 'datetime', filterable: false },
          { text: 'Balance', align: 'end', value: 'amount', width: 100, filterable: false },
        ]
      },
      paymentHeaders () {
        return [
          { text: 'Date', align: 'start', value: 'datetime' },
          { text: 'Number', align: 'end', value: 'number' },
          { text: 'Amount', align: 'center', value: 'amount' },
          { text: 'Name', align: 'start', value: 'name' },
          { text: 'Description', align: 'start', value: 'description' },
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
      axios.get('https://admin.montelcompany.me/api/noTerminals') // TODO: объеденить
        .then(response => {
          this.noTerminals = response.data.map((item) => {
            return {
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
      sumField (key, base) {
        return base.reduce((a, b) => parseInt(a) + (parseInt(b[key]) || 0), 0)
      },
      loadDetails ({ item, date = 0 }) {
        axios.get('https://admin.montelcompany.me/api/terminals?dt=payments&id=' + item.id)
          .then(response => {
            this.payments = response.data
            this.arrayEvents = this.payments.map(item => new Date(item.datetime).toISOString().substr(0, 10))
          })
          .catch(function (error) {
            console.log(error)
          })
        this.selected = item
        // this.hysBalance = item.balance
      },
      loadDetailsNt ({ item }) {
        axios.get('https://admin.montelcompany.me/api/terminals?dt=payments&id=' + item.place) // TODO: объеденить
          .then(response => {
            this.payments = response.data
            this.arrayEvents = this.payments.map(item => new Date(item.datetime).toISOString().substr(0, 10))
          })
          .catch(function (error) {
            console.log(error)
          })
        this.selected = item.place
        // this.hysBalance = item.balance
      },
      getTime (datetime) {
        datetime = new Date(new Date(datetime).getTime() - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleTimeString()
      },
      selectHysDate () {
        axios.get('https://admin.montelcompany.me/api/terminals?dt=payments&id=' + this.selected.id + '&fromdate=' + this.date)
          .then(response => {
            this.payments = response.data
          })
          .catch(function (error) {
            console.log(error)
          })
        this.menu2 = false
        this.menu3 = false
      },
      getDateTime (datetime) {
        datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
      },
      restart (id) {
        this.informColor = 'warning'
        this.informText = 'UNDER CONSTRUCTION'
        this.informSnackbar = true
      },
    },
  }
</script>
