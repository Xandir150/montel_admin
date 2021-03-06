import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'hash',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      component: () => import('@/views/dashboard/Index'),
      children: [
        // Dashboard
        {
          name: 'Dashboard',
          path: '',
          component: () => import('@/views/dashboard/Dashboard'),
        },
        // Pages
        {
          name: 'Customer Profile',
          path: 'pages/customer/:id',
          props: true,
          component: () => import('@/views/dashboard/pages/CustomerProfile'),
        },
        {
          name: 'Paynments',
          path: 'tables/payments/:customerid',
          props: true,
          component: () => import('@/views/dashboard/tables/Payments'),
        },
        {
          name: 'Options',
          path: 'pages/options',
          component: () => import('@/views/dashboard/pages/Options'),
        },
        {
          name: 'Messages',
          path: 'tables/messages',
          component: () => import('@/views/dashboard/tables/Messages'),
        },
        {
          name: 'Notifications',
          path: 'components/notifications',
          component: () => import('@/views/dashboard/component/Notifications'),
        },
        {
          name: 'Icons',
          path: 'components/icons',
          component: () => import('@/views/dashboard/component/Icons'),
        },
        {
          name: 'Typography',
          path: 'components/typography',
          component: () => import('@/views/dashboard/component/Typography'),
        },
        // Tables
        {
          name: 'Customers',
          path: 'tables/customers',
          component: () => import('@/views/dashboard/tables/Customers'),
        },
        {
          name: 'Reports',
          path: 'tables/reports',
          component: () => import('@/views/dashboard/tables/Reports'),
        },
        {
          name: 'Invoices',
          path: 'tables/invoices/:invoiceid',
          props: true,
          component: () => import('@/views/dashboard/tables/Invoices'),
        },
        {
          name: 'Terminals',
          path: 'tables/terminals',
          component: () => import('@/views/dashboard/tables/Terminals'),
        },
        // Maps
        {
          name: 'Google Maps',
          path: 'maps/google-maps',
          component: () => import('@/views/dashboard/maps/GoogleMaps'),
        },
        // Upgrade
        {
          name: 'Upgrade',
          path: 'upgrade',
          component: () => import('@/views/dashboard/Upgrade'),
        },
        {
          name: 'Tabs',
          path: '/component/tabs',
          component: () => import('@/views/dashboard/component/Notifications'),
        },
      ],
    },
  ],
})
