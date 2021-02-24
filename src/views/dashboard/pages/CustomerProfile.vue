<template>
  <v-container
    id="customer-profile"
    fluid
    tag="section"
  >
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
            v-model="editedItem.balance"
            :rules="rules.digits"
            label="Сумма"
            suffix="€"
          />
          <v-select
            v-model="editedItem.place"
            :items="places"
            :rules="rules.name"
            label="Способ оплаты"
            required
          />
          <v-text-field
            v-model="editedItem.description"
            :rules="rules.name"
            required
            label="Комментарий"
            counter
            maxlength="64"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            :disabled="!formIsValid"
            color="success"
            text
            @click="changeBalance()"
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
    <v-dialog
      v-model="dialogChangeTariff"
      max-width="390"
    >
      <v-card>
        <v-card-title>
          Do you really want to change the tariff for {{ customer.phone }} to "{{ tariffName }}"?

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
    <v-row justify="center">
      <v-col
        cols="12"
        md="8"
      >
        <base-material-card :color="headerColor.color">
          <template #heading>
            <div class="display-2 font-weight-light">
              <strong>{{ customer.name }}</strong>
            </div>

            <div class="subtitle-1 font-weight-light">
              из города {{ customer.city }}
            </div>
          </template>

          <v-form>
            <v-container class="py-0">
              <v-row>
                <v-col
                  v-if="id < 1"
                  cols="12"
                >
                  <v-text-field
                    v-model="customer.phone"
                    :rules="rules.digits"
                    prepend-icon="mdi-phone"
                    label="Phone"
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="4"
                >
                  <v-text-field
                    v-model="customer.name"
                    :prepend-icon="icon"
                    label="Имя клиента"
                    @click:prepend="changeIcon"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <v-text-field
                    v-model="customer.city"
                    prepend-icon="mdi-home-city"
                    label="Город"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="4"
                >
                  <v-text-field
                    v-model="customer.email"
                    prepend-icon="mdi-email"
                    label="Email Address"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="6"
                >
                  <v-text-field
                    v-model="customer.Facebook"
                    prepend-icon="mdi-facebook-messenger"
                    label="Facebook"
                  />
                </v-col>

                <v-col
                  cols="12"
                  md="6"
                >
                  <v-text-field
                    v-model="customer.telegram"
                    label="Telegram"
                    prepend-icon="mdi-telegram"
                  />
                </v-col>

                <!-- <v-col cols="12">
                  <v-text-field
                    disabled
                    label="Адрес"
                    class="purple-input"
                  />
                </v-col> -->
                <v-col cols="12">
                  <v-textarea
                    v-model="customer.description"
                    class="purple-input"
                    label="Примечания"
                    value=""
                  />
                </v-col>

                <v-col
                  cols="12"
                  class="text-right"
                >
                  <v-btn
                    color="success"
                    class="mr-0"
                    :disabled="!formIsValid2"
                    @click="updateProile"
                  >
                    {{ buttonName }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-form>
        </base-material-card>
      </v-col>

      <v-col
        v-if="id > 0"
        cols="12"
        md="4"
      >
        <base-material-card
          class="v-card-profile"
          :color="headerColor.color"
          :title="headerColor.text"
          :text="customer.phone"
        >
          <v-card-text class="text-center">
            <h6 class="display-1 mb-1 grey--text">
              Клиент с {{ customer.created }}
            </h6>
            <h6 class="display-1 mb-1 grey--text">
              Последний платеж {{ getDateTime(customer.lastpaydate) }}
            </h6>
            <h4 class="display-2 font-weight-light mb-3 black--text">
              Баланс {{ customer.balance }} €
            </h4>
            <v-select
              v-model="customer.tariff"
              :items="tariffs"
              color="primary"
              label="Tariff"
              @change="changeTariff"
            />
            <div class="my-2">
              <v-btn
                small
                outlined
                color="primary"
                class="ma-2"
                :to="{ path: `/tables/payments/${customer.phone}`}"
              >
                <v-icon left>
                  mdi-account-arrow-left
                </v-icon> {{ customer.payments }} €
              </v-btn>
              <v-btn
                small
                outlined
                color="primary"
                class="ma-2"
                :to="{ path: `/tables/payments/${customer.phone}`}"
              >
                <v-icon left>
                  mdi-account-arrow-right
                </v-icon> {{ customer.cexpenses }} €
              </v-btn>
            </div>
            <div class="my-2">
              <v-btn
                class="ma-2"
                small
                color="primary"
                @click="setBalance(1)"
              >
                Оплата
              </v-btn>
              <v-btn
                class="ma-2"
                small
                color="primary"
                @click="setBalance(0)"
              >
                Расход
              </v-btn>
            </div>
          </v-card-text>
        </base-material-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'CustomerProfile',
    props: {
      id: {
        type: String,
        required: true,
      },
    },
    data: () => ({
      dialogChangeBalance: false,
      editedItem: {
        name: '',
        balance: 0,
        email: 0,
        phone: 0,
        route: 0,
        place: '',
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
        place: '',
        description: '',
      },
      places: ['Терминал', 'Безналичный расчет', 'Наличные', 'Альфа-банк', 'Тинькофф', 'CKB', 'Сбербанк', 'Почта'],
      rules: {
        name: [val => (val || '').length > 0 || 'Это обязательное поле'],
        digits: [val => Number.isInteger(Number(val * 100)) || 'Должно быть ЧИСЛО!'],
      },
      newUserId: '0',
      informSnackbar: false,
      timeout: 3000,
      informColor: 'warning',
      informText: 'UNDER CONSTRUCTION',
      dialogChangeTariff: false,
      newTariffId: -1,
      customer: {
        id: -1,
        name: '',
        email: '',
        phone: '',
        city: '',
        telegram: '',
        Facebook: '',
        credit: 0,
        decription: '',
        status: 1,
        tPercent: 1,
        tDicsount: 1,
      },
      tariffs: [],
      iconIndex: 0,
      icons: [
        'mdi-emoticon',
        'mdi-emoticon-cool',
        'mdi-emoticon-dead',
        'mdi-emoticon-excited',
        'mdi-emoticon-happy',
        'mdi-emoticon-neutral',
        'mdi-emoticon-sad',
        'mdi-emoticon-tongue',
      ],
    }),
    computed: {
      formTitleChangeBalance () {
        return this.editedItem.route === 0 ? 'Уменьшение' : 'Добавление'
      },
      formIsValid () {
        return this.editedItem.place && this.editedItem.description && Number.isInteger(this.editedItem.balance * 100)
      },
      formIsValid2 () {
        return Number.isInteger(this.customer.phone * 1) && this.customer.name.length > 3 && this.customer.phone.length === 8
      },
      tariffName () {
        return this.newTariffId >= 0 ? this.tariffs[this.newTariffId].text : ''
      },
      buttonName () {
        return this.id > 0 ? 'Обновить профиль' : 'Добавить клиента'
      },
      headerColor () {
        return this.customer.status === '0' ? { color: 'red', text: 'Отключен' } : { color: 'success', text: 'Активный' }
      },
      icon () {
        return this.icons[this.iconIndex]
      },
    },
    watch: {
      dialog (val) {
        val || this.close()
      },
      newUserId () {
        console.log(this.newUserId)
      },
    },
    created () {
      if (this.id > 0) { this.getCustomer() }
      // this.getTariffs()
    },
    mounted () {
      axios.get('https://admin.montelcompany.me/api/tariffs')
        .then(response => {
          this.tariffs = response.data.map(item => {
            return { text: item.Name, value: item.id }
          })
        })
    },
    methods: {
      getCustomer: function (app = this) {
        axios
          .get('https://admin.montelcompany.me/api/getCustomer?id=' + this.id)
          .then(function (response) {
            app.customer = response.data
          })
          .catch(function (error) {
            console.log(error)
          })
      },
      // getTariffs: function (app = this) {
      //   axios.get('https://admin.montelcompany.me/api/tariffs')
      //     .then(response => {
      //       this.tariffs = response.data.map(item => {
      //         return { text: item.Name, value: item.id }
      //       })
      //     })
      // },
      changeTariff (selectObj) {
        this.newTariffId = selectObj
        this.dialogChangeTariff = true
      },
      confirmChangeTariff () {
        axios.post('https://admin.montelcompany.me/api/newTariff', {
          id: this.customer.id,
          newTariffId: this.newTariffId,
        })
        this.dialogChangeTariff = false
        this.newTariffId = -1
      },
      updateProile (app = this) {
        axios.post('https://admin.montelcompany.me/api/updateProfile', {
          id: this.customer.id,
          name: this.customer.name,
          email: this.customer.email,
          phone: this.customer.phone,
          city: this.customer.city,
          telegram: this.customer.telegram,
          Facebook: this.customer.Facebook,
          credit: this.customer.credit,
          decription: this.customer.decription,
          status: this.customer.status,
          tPercent: this.customer.tPercent,
          tDicsount: this.customer.tDicsount,
        })
          .then(function (response) {
            if (app.id > 0) { app.msgSuccess('Profile has ben updated') } else {
              window.location = `#/pages/customer/${response.data}`
            }
          })
          .catch(function (error) {
            console.log(error)
            app.msgError(error)
          })
      },
      changeIcon () {
        this.iconIndex === this.icons.length - 1
          ? this.iconIndex = 0
          : this.iconIndex++
      },
      getDateTime (datetime) {
        datetime = new Date(new Date(datetime) - this.getTimeOffset() * 60 * 1000)
        return new Date(datetime).toLocaleString()
      },
      setBalance (route) {
        this.editedItem.phone = this.customer.phone
        this.editedItem.name = this.customer.name
        this.editedItem.route = route
        this.editedItem.balance = 0
        this.dialogChangeBalance = true
      },
      changeBalance () {
        if (this.editedItem.route === 0) { this.editedItem.balance = this.editedItem.balance * -1 }
        axios.post('https://admin.montelcompany.me/api/chargeCustom', {
          number: this.editedItem.phone,
          place: this.editedItem.place,
          amount: this.editedItem.balance,
          provider: 'montel',
          description: this.editedItem.description,
        })
        this.informColor = 'success'
        this.informText = 'Успешно изменен баланс ' + this.editedItem.phone + ' на ' + this.editedItem.balance
        this.informSnackbar = true
        this.this.customer.balance = (this.editedItem.balance * 100 + this.this.customer.balance * 100) / 100
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
      close () {
        this.dialogChangeTariff = false
        this.dialogChangeBalance = false
        this.$nextTick(() => {
          this.newTariffId = -1
          this.editedItem = Object.assign({}, this.defaultItem)
        })
      },
    },
  }
</script>
