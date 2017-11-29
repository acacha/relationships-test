import Vuex from 'vuex'

const debug = process.env.NODE_ENV !== 'production'

import Form from './Form'

const initialForm = new Form({ givenName: '' })

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
      console.log('POST VUEX ACTION')
      return context.state.form.post('/api/v1/person')
    }
  },
  strict: debug
})