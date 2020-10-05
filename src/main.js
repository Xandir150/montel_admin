import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './plugins/base'
import './plugins/chartist'
import './plugins/vee-validate'
import vuetify from './plugins/vuetify'
import i18n from './i18n'
import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

Vue.config.productionTip = false

const requireComponent = require.context(
  // Относительный путь до каталога компонентов
  './components',
  // Обрабатывать или нет подкаталоги
  false,
  // Регулярное выражение для определения файлов базовых компонентов
  /[A-Z]\w+\.(vue|js)$/,
)

requireComponent.keys().forEach(fileName => {
  // Получение конфигурации компонента
  const componentConfig = requireComponent(fileName)

  // Получение имени компонента в PascalCase
  const componentName = upperFirst(
    camelCase(fileName.replace(/^\.\//, '').replace(/\.\w+$/, '')),
  )

  // Глобальная регистрация компонента
  Vue.component(
    componentName,
    // Поиск опций компонента в `.default`, который будет существовать,
    // если компонент экспортирован с помощью `export default`,
    // иначе будет использован корневой уровень модуля.
    componentConfig.default || componentConfig,
  )
})
Vue.mixin({
  methods: {
    getTimeOffset: function () {
      return new Date().getTimezoneOffset()
    },
  },
})
new Vue({
  router,
  store,
  vuetify,
  i18n,
  render: h => h(App),
}).$mount('#app')
