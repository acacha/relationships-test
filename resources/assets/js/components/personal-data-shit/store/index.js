import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

import Form from 'acacha-forms'
import axios from 'axios'

const initialForm = new Form({
  identifier_id: '',
  identifier: '',
  identifier_type: '',
  givenName: '',
  surname1: '',
  surname2: '',
  birthdate: '',
  birthplace_id: '',
  gender: ''
})

export default new Vuex.Store({
  state: {
    form : initialForm
  },
  mutations: {
    updateForm: function (state, {field, value}) {
      Object.assign(state.form, {
        [field]: value
      });
    },
    clear (state) {
      console.log('executing mutation clear! ' + state)
      state.form = initialForm
    },
  },
  actions: {
    post(context) {
      return context.state.form.post('/api/v1/person')
    }
  },
  strict: debug
})