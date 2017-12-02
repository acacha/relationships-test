export default {
  methods: {
    fetchPerson(id) {
      this.currentStatus = STATUS_LOADING
      let url = '/api/v1/person/' + id
      axios.get(url).then( response => {
        this.mapPersonToForm(response.data)
        this.currentStatus = STATUS_UPDATING_EXISTING_PERSON
        this.updateFullName()
      }).catch( (error) => {
        console.log(error)
      }).then( () => {
        this.personId = id
      })
    },
  }
}