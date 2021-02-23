<template>
  <v-container
    id="options"
    fluid
    tag="section"
  >
    <v-row justify="center">
      <v-col>
        <base-material-card color="primary">
          <template #heading>
            <div class="display-2 font-weight-light">
              <strong> {{ $t('settings') }}  </strong>
            </div>

            <div class="subtitle-1 font-weight-light">
              {{ $t('settings_subt') }}
            </div>
          </template>

          <v-form>
            <v-container class="py-0">
              <v-row>
                <v-col
                  cols="12"
                  md="4"
                >
                  <!-- <v-text-field
                    v-model="customer.name"
                    :prepend-icon="icon"
                    label="Имя клиента"
                  /> -->
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <!-- <v-text-field
                    v-model="customer.city"
                    prepend-icon="mdi-home-city"
                    label="Город"
                  /> -->
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <!-- <v-text-field
                    v-model="customer.email"
                    prepend-icon="mdi-email"
                    label="Email Address"
                  /> -->
                </v-col>

                <v-col
                  cols="12"
                  md="6"
                >
                  <!-- <v-text-field
                    v-model="customer.Facebook"
                    prepend-icon="mdi-facebook-messenger"
                    label="Facebook"
                  /> -->
                </v-col>

                <v-col
                  cols="12"
                  md="6"
                >
                  <!-- <v-text-field
                    v-model="customer.telegram"
                    label="Telegram"
                    prepend-icon="mdi-telegram"
                  /> -->
                </v-col>

                <v-col cols="12">
                  <!-- <v-text-field
                    disabled
                    label="Адрес"
                    class="purple-input"
                  /> -->
                </v-col>
                <v-col cols="12">
                  <!-- <v-textarea
                    v-model="customer.description"
                    class="purple-input"
                    label="Примечания"
                    value=""
                  /> -->
                </v-col>

                <v-col
                  cols="12"
                  class="text-right"
                >
                  <!-- <v-btn
                    color="success"
                    class="mr-0"
                    @click="updateProile"
                  >
                    Обновить профиль
                  </v-btn> -->
                </v-col>
              </v-row>
            </v-container>
          </v-form>
          <v-card-actions>
            <v-btn
              text
              color="primary"
            >
              {{ $t('save') }}
            </v-btn>
          </v-card-actions>
        </base-material-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'DashboardPagesTimeline',

    data: () => ({
      menu: false,
      options: {},
    }),
    created () {
      this.getOptions()
    },
    methods: {
      getOptions: function (app = this) {
        axios
          .get('https://admin.montelcompany.me/api/getOptions')
          .then(function (response) {
            app.options = response.data
            console.log(response.data)
          })
          .catch(function (error) {
            console.log(error)
          })
      },
    },
  }
</script>
