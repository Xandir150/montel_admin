<template>
  <v-container
    id="customers"
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
      icon="mdi-android-messages"
      :title="$t('messages')"
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
            mdi-email-send
          </v-icon>
          Исходящие
        </v-tab>
        <v-tab
          class="mr-3"
        >
          <v-icon class="mr-2">
            mdi-email-receive
          </v-icon>
          Входящие
        </v-tab>
        <!-- <v-tab>
          <v-icon class="mr-2">
            mdi-email-multiple
          </v-icon>
          Диалоги
        </v-tab>
        <v-tab>
          <v-icon class="mr-2">
            mdi-email-plus
          </v-icon>
          Новое
        </v-tab> -->
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
            :items="sms.data"
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
              color="primary"
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
                <!-- <v-dialog
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
                      New send list
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
                </v-dialog> -->
              </v-toolbar>
            </template>
            <template #[`item.phone`]="{ item }">
              <v-list-item two-line>
                <v-list-item-content>
                  <v-list-item-title>{{ item.phone }}</v-list-item-title>
                  <v-list-item-subtitle>{{ getDateTime(item.date) }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>
            </template>
            <template #[`item.is_send_to_phone`]="{ item }">
              <v-tooltip bottom>
                <template #activator="{ on, attrs }">
                  <v-icon
                    v-if="item.is_send_to_phone == 1"
                    v-bind="attrs"
                    class="mr-2"
                    color="green"
                    v-on="on"
                  >
                    mdi-check-circle
                  </v-icon>
                  <v-icon
                    v-else
                    class="mr-2"
                  >
                    mdi-check-circle
                  </v-icon>
                </template>
                <span>To phone: {{ getDateTime(item.send_to_phone) }}</span>
              </v-tooltip>

              <v-tooltip bottom>
                <template #activator="{ on, attrs }">
                  <v-icon
                    v-if="item.is_send == 1"
                    v-bind="attrs"
                    class="mr-2"
                    color="green"
                    v-on="on"
                  >
                    mdi-check-circle
                  </v-icon>
                  <v-icon
                    v-else-if="item.is_error_send == 1"
                    v-bind="attrs"
                    class="mr-2"
                    color="red"
                    v-on="on"
                  >
                    mdi-alert-circle
                  </v-icon>
                  <v-icon
                    v-else
                    class="mr-2"
                  >
                    mdi-check-circle
                  </v-icon>
                </template>
                <span v-if="item.is_error_send == 1">
                  Error: {{ getDateTime(item.error_date) }}
                </span>
                <span v-else>
                  Sended: {{ getDateTime(item.send) }}
                </span>
              </v-tooltip>

              <v-tooltip bottom>
                <template #activator="{ on, attrs }">
                  <v-icon
                    v-if="item.is_delivered == 1"
                    v-bind="attrs"
                    class="mr-2"
                    color="green"
                    v-on="on"
                  >
                    mdi-check-circle
                  </v-icon>
                  <v-icon
                    v-else
                    class="mr-2"
                  >
                    mdi-check-circle
                  </v-icon>
                </template>
                <span>Delivered: {{ getDateTime(item.send_to_phone) }}</span>
              </v-tooltip>
            </template>
            <template #expanded-item="{ item }">
              <td :colspan="headers.length">
                <v-container>
                  <v-row no-gutters>
                    <div class="text--primary">
                      {{ item.msg }}
                    </div>
                  </v-row>
                </v-container>
              </td>
            </template>
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
      options: {},
      sms: {},
      progressBar: true,
      informSnackbar: false,
      timeout: 3000,
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      dialog: false,
      expanded: [],
      search: '',
      singleExpand: true,
      tabs: 0,
    }),

    computed: {
      headers () {
        return [
          { text: 'phone', align: 'start', value: 'phone' },
          // { text: 'date', align: 'center', value: 'date' },
          { text: 'status', align: 'center', value: 'is_send_to_phone', width: 120 },
          // { text: 'is_send', align: 'center', value: 'is_send' },
          // { text: 'is_delivered', align: 'center', value: 'is_delivered' },
        ]
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      immediate: true,
      options () {
        this.outbox()
      },
      sms () {
        this.progressBar = false
      },
    },

    // created () {
    //   // this.getCustomers()
    //   console.log('created')
    //   this.getTariffs()
    // },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/getOptions')
        .then((response) => {
          this.options = response.data
        })
        .catch((error) => {
          console.log(error)
        })
    },
    methods: {
      outbox () {
        axios.get('https://admin.montelcompany.me/api/outbox_sms')
          .then((response) => {
            this.sms = response.data
          })
          .catch((error) => {
            console.log(error)
          })
      },
      tabChange: async function (obj, app = this) {
        app.progressBar = true
        app.tabs = obj
        if (obj === 0) { app.outbox() } else if (obj === 1) {
          axios.get('https://admin.montelcompany.me/api/inbox_sms')
            .then((response) => {
              this.sms = response.data
            })
            .catch((error) => {
              console.log(error)
            })
        }
      },
      getDateTime (datetime) {
        // datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
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
    },
  }
</script>
