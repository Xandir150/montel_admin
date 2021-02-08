<template>
  <v-container
    id="dashboard"
    fluid
    tag="section"
  >
    <v-row>
      <v-col
        cols="12"
        lg="4"
      >
        <base-material-chart-card
          :data="invoicesShart.data"
          :options="invoicesShart.options"
          :responsive-options="invoicesShart.responsiveOptions"
          color="#E91E63"
          hover-reveal
          type="Bar"
        >
          <!-- <template #reveal-actions>
            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  color="info"
                  icon
                  v-on="on"
                >
                  <v-icon
                    color="info"
                  >
                    mdi-refresh
                  </v-icon>
                </v-btn>
              </template>

              <span>Refresh</span>
            </v-tooltip>

            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  light
                  icon
                  v-on="on"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>

              <span>Change Date</span>
            </v-tooltip>
          </template> -->

          <h4 class="card-title font-weight-light mt-2 ml-2">
            Invoices
          </h4>

          <p class="d-inline-flex font-weight-light ml-2 mt-1">
            Amount invices per month
          </p>

          <!-- <template #actions>
            <v-icon
              class="mr-1"
              small
            >
              mdi-clock-outline
            </v-icon>
            <span class="caption grey--text font-weight-light">updated 10 minutes ago</span>
          </template> -->
        </base-material-chart-card>
      </v-col>

      <v-col
        cols="12"
        lg="4"
      >
        <base-material-chart-card
          :data="dailySalesChart.data"
          :options="dailySalesChart.options"
          color="success"
          hover-reveal
          type="Line"
        >
          <template #reveal-actions>
            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  color="info"
                  icon
                  v-on="on"
                >
                  <v-icon
                    color="info"
                  >
                    mdi-refresh
                  </v-icon>
                </v-btn>
              </template>

              <span>Refresh</span>
            </v-tooltip>

            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  light
                  icon
                  v-on="on"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>

              <span>Change Date</span>
            </v-tooltip>
          </template>

          <h4 class="card-title font-weight-light mt-2 ml-2">
            Daily Payments
          </h4>

          <p class="d-inline-flex font-weight-light ml-2 mt-1">
            <v-icon
              color="green"
              small
            >
              mdi-arrow-up
            </v-icon>
            <span class="green--text">55% </span>&nbsp; increase in today's payments
          </p>

          <template #actions>
            <v-icon
              class="mr-1"
              small
            >
              mdi-clock-outline
            </v-icon>
            <span class="caption grey--text font-weight-light">updated 4 minutes ago</span>
          </template>
        </base-material-chart-card>
      </v-col>

      <v-col
        cols="12"
        lg="4"
      >
        <base-material-chart-card
          :data="dataCompletedTasksChart.data"
          :options="dataCompletedTasksChart.options"
          hover-reveal
          color="info"
          type="Line"
        >
          <template #reveal-actions>
            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  color="info"
                  icon
                  v-on="on"
                >
                  <v-icon
                    color="info"
                  >
                    mdi-refresh
                  </v-icon>
                </v-btn>
              </template>

              <span>Refresh</span>
            </v-tooltip>

            <v-tooltip bottom>
              <template #activator="{ attrs, on }">
                <v-btn
                  v-bind="attrs"
                  light
                  icon
                  v-on="on"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>

              <span>Change Date</span>
            </v-tooltip>
          </template>

          <h3 class="card-title font-weight-light mt-2 ml-2">
            Balances
          </h3>

          <p class="d-inline-flex font-weight-light ml-2 mt-1">
            Last Last Campaign Performance
          </p>

          <template #actions>
            <v-icon
              class="mr-1"
              small
            >
              mdi-clock-outline
            </v-icon>
            <span class="caption grey--text font-weight-light">campaign sent 26 minutes ago</span>
          </template>
        </base-material-chart-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="3"
      >
        <base-material-stats-card
          color="info"
          icon="mdi-account-arrow-left"
          title="New customers"
          value="0"
          sub-icon="mdi-clock"
          sub-text="Just Updated"
          @click="color"
        />
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="3"
      >
        <router-link
          :to="{ path: `/tables/terminals`}"
          tag="span"
          exact
        >
          <base-material-stats-card
            color="primary"
            icon="mdi-wallet"
            title="В терминалах"
            :value="mit"
            :style="{ cursor: 'pointer', font: 'bold' }"
            sub-icon="mdi-clock"
            sub-text="Обновлено сейчас"
          />
        </router-link>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="3"
      >
        <base-material-stats-card
          color="success"
          icon="mdi-cash-plus"
          title=""
          value="0.00 €"
          sub-icon="mdi-calendar"
          sub-text="Never"
        />
      </v-col>

      <v-col
        cols="12"
        sm="6"
        lg="3"
      >
        <base-material-stats-card
          color="orange"
          icon="mdi-account"
          title="Customers"
          value="3640"
          sub-icon="mdi-alert"
          sub-icon-color="red"
          sub-text="Get More Space..."
        />
      </v-col>

      <v-col
        cols="12"
        md="6"
      >
        <base-material-card
          color="warning"
          class="px-5 py-3"
        >
          <template #heading>
            <div class="display-2 font-weight-light">
              Счета
            </div>

            <div class="subtitle-1 font-weight-light">
              Последние счета операторов
            </div>
          </template>
          <v-card-text>
            <v-data-table
              :headers="headers"
              :items="invoices"
            >
              <template #[`item.csumm`]="{ item }">
                <p class="font-weight-bold">
                  {{ Math.round((parseFloat(item.amount) + parseFloat(item.overfee) - parseFloat(item.discount)) * 100) / 100 }}
                </p>
              </template>
            </v-data-table>
          </v-card-text>
        </base-material-card>
      </v-col>

      <!-- <v-col
        cols="12"
        md="6"
      >
        <base-material-card class="px-5 py-3">
          <template v-slot:heading>
            <v-tabs
              v-model="tabs"
              background-color="transparent"
              slider-color="white"
            >
              <span
                class="subheading font-weight-light mx-3"
                style="align-self: center"
              >Tasks:</span>
              <v-tab class="mr-3">
                <v-icon class="mr-2">
                  mdi-bug
                </v-icon>
                Bugs
              </v-tab>
              <v-tab class="mr-3">
                <v-icon class="mr-2">
                  mdi-code-tags
                </v-icon>
                Website
              </v-tab>
              <v-tab>
                <v-icon class="mr-2">
                  mdi-cloud
                </v-icon>
                Server
              </v-tab>
            </v-tabs>
          </template>

          <v-tabs-items
            v-model="tabs"
            class="transparent"
          >
            <v-tab-item
              v-for="n in 3"
              :key="n"
            >
              <v-card-text>
                <template v-for="(task, i) in tasks[tabs]">
                  <v-row
                    :key="i"
                    align="center"
                  >
                    <v-col cols="1">
                      <v-list-item-action>
                        <v-checkbox
                          v-model="task.value"
                          color="secondary"
                        />
                      </v-list-item-action>
                    </v-col>

                    <v-col cols="9">
                      <div
                        class="font-weight-light"
                        v-text="task.text"
                      />
                    </v-col>

                    <v-col
                      cols="2"
                      class="text-right"
                    >
                      <v-icon class="mx-1">
                        mdi-pencil
                      </v-icon>
                      <v-icon
                        color="error"
                        class="mx-1"
                      >
                        mdi-close
                      </v-icon>
                    </v-col>
                  </v-row>
                </template>
              </v-card-text>
            </v-tab-item>
          </v-tabs-items>
        </base-material-card>
      </v-col> -->
    </v-row>
  </v-container>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'DashboardDashboard',
    data () {
      return {
        dailySalesChart: {
          data: {
            labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
            series: [
              [12, 17, 7, 17, 23, 18, 38],
            ],
          },
          options: {
            lineSmooth: this.$chartist.Interpolation.cardinal({
              tension: 0,
            }),
            low: 0,
            high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          },
        },
        dataCompletedTasksChart: {
          data: {
            labels: ['Ja', 'Fe', 'Ma', 'Ap', 'Mai', 'Ju', 'Jul', 'Au', 'Se', 'Oc', 'No', 'De'],
            series: [
              [230, 750, 450, 300, 280, 240, 200, 190],
            ],
          },
          options: {
            lineSmooth: this.$chartist.Interpolation.cardinal({
              tension: 0,
            }),
            low: 0,
            high: 1000, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
            chartPadding: {
              top: 0,
              right: 0,
              bottom: 0,
              left: 0,
            },
          },
        },
        invoicesShart: {
          data: {
            labels: ['Ja', 'Fe', 'Ma', 'Ap', 'Mai', 'Ju', 'Jul', 'Au', 'Se', 'Oc', 'No', 'De'],
            series: [
              [542, 443, 320, 780, 553, 453, 326, 434, 568, 610, 756, 895],
              [230, 750, 450, 300, 280, 240, 200, 190],
            ],
          },
          options: {
            axisX: {
              showGrid: false,
            },
            low: 0,
            high: 1000,
            chartPadding: {
              top: 0,
              right: 5,
              bottom: 0,
              left: 0,
            },
          },
          responsiveOptions: [
            ['screen and (max-width: 640px)', {
              seriesBarDistance: 5,
              axisX: {
                labelInterpolationFnc: function (value) {
                  return value[0]
                },
              },
            }],
          ],
        },
        items: [
          {
            id: 1,
            name: 'Dakota Rice',
            country: 'Niger',
            city: 'Oud-Tunrhout',
            salary: '$35,738',
          },
          {
            id: 2,
            name: 'Minerva Hooper',
            country: 'Curaçao',
            city: 'Sinaai-Waas',
            salary: '$23,738',
          },
          {
            id: 3,
            name: 'Sage Rodriguez',
            country: 'Netherlands',
            city: 'Overland Park',
            salary: '$56,142',
          },
          {
            id: 4,
            name: 'Philip Chanley',
            country: 'Korea, South',
            city: 'Gloucester',
            salary: '$38,735',
          },
          {
            id: 5,
            name: 'Doris Greene',
            country: 'Malawi',
            city: 'Feldkirchen in Kārnten',
            salary: '$63,542',
          },
        ],
        tabs: 0,
        tasks: {
          0: [
            {
              text: 'Sign contract for "What are conference organizers afraid of?"',
              value: true,
            },
            {
              text: 'Lines From Great Russian Literature? Or E-mails From My Boss?',
              value: false,
            },
            {
              text: 'Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit',
              value: false,
            },
            {
              text: 'Create 4 Invisible User Experiences you Never Knew About',
              value: true,
            },
          ],
          1: [
            {
              text: 'Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit',
              value: true,
            },
            {
              text: 'Sign contract for "What are conference organizers afraid of?"',
              value: false,
            },
          ],
          2: [
            {
              text: 'Lines From Great Russian Literature? Or E-mails From My Boss?',
              value: false,
            },
            {
              text: 'Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit',
              value: true,
            },
            {
              text: 'Sign contract for "What are conference organizers afraid of?"',
              value: true,
            },
          ],
        },
        list: {
          0: false,
          1: false,
          2: false,
        },
        invoices: [],
        mit: '0 €',
      }
    },
    computed: {
      headers () {
        return [
          { text: 'Дата', align: 'center', value: 'date', filterable: false },
          { text: 'Повайдер', align: 'center', value: 'provider', filterable: false },
          { text: 'Номер', align: 'start', value: 'doc_num' },
          { text: 'Сумма', align: 'center', value: 'csumm', filterable: false },
          { text: 'С клиентов', align: 'center', value: 'tamount', filterable: false },
        ]
      },
    },
    created () {
      this.getData()
    },
    methods: {
      complete (index) {
        this.list[index] = !this.list[index]
      },
      color () {
        this.$vuetify.theme.themes[this.isDark ? 'dark' : 'light'].primary = '#E91E63'
      },
      getData: function (app = this) {
        axios
          .get('https://admin.montelcompany.me/api/dashboardGetInvByMonth')
          .then(response => {
            this.invoicesShart.data = response.data
            this.invoicesShart.options.high = Math.max(...this.invoicesShart.data.series[0]) + 200
          })
          .catch(function (error) {
            console.log(error)
          })
        axios
          .get('https://admin.montelcompany.me/api/getInvoicesList')
          .then(response => {
            this.invoices = response.data
          })
          .catch(function (error) {
            console.log(error)
          })
        axios
          .get('https://admin.montelcompany.me/api/dashboardGetCacheInterms')
          .then(response => {
            this.mit = response.data / 100 + ' €'
          })
          .catch(function (error) {
            console.log(error)
          })
      },
    },
  }
</script>
