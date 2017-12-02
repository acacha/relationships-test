import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

import Form from 'acacha-forms'

export default new Vuex.Store({
  state: {
    form : new Form({
      identifier_id: '',
      identifier: '',
      identifier_type: '',
      givenName: '',
      surname1: '',
      surname2: '',
      birthdate: '',
      birthplace_id: '',
      gender: '' })
  },
  mutations: {
    increment (state) {
      state.form++
    }
  },
  strict: debug
})