<!-- Vue component -->
<template>
    <div class="form-group">
        <label for="fullname">Full name</label>
        <multiselect id="fullname" v-model="internalFullname" :options="fullnames" :custom-label="customLabel"
                     @select="fullnameHasBeenSelected"
                     placeholder="Select name" :disabled="disabled" :loading="loading"></multiselect>
    </div>
</template>

<script>
  import Multiselect from 'vue-multiselect'
  import axios from 'axios'

  export default {
    components: { Multiselect },
    data () {
      return {
        internalFullname: null,
        fullnames: [],
        loading: false
      }
    },
    props: {
      disabled: {
        type: Boolean,
        default: false
      },
      identifier: {
        default: null
      },
      fullname: {
        type: Object,
        default: null
      }
    },
    watch: {
      identifier: function(newVal) {
        this.updateSelectedFullnameByIdentifier(newVal)
      },
      fullname: function(newVal) {
        if (this.fullnames[newVal]) this.internalFullname = newVal
        this.addNewFullNameAndSelect(newVal)
      }
    },
    methods: {
      updateSelectedFullnameByIdentifier(id) {
        this.internalFullname = this.findFullNameByIdentifierId(id)
      },
      addNewFullNameAndSelect(fullname) {
        this.fullnames.push(fullname)
        this.internalFullname = fullname
      },
      findFullNameByIdentifierId(id){
        return this.fullnames.find((fullname) => {
          return fullname.identifier_id === id
        })
      },
      fullnameHasBeenSelected(fullname) {
        this.$emit('selected',fullname)
      },
      customLabel({ name, identifier}) {
        return identifier ? `${name} - ${identifier}` : `${name}`
      },
      fetchFullnames() {
        let url = '/api/v1/fullname'
        this.loading = true
        axios.get(url).then((response) => {
          this.fullnames = response.data
        }).catch((error) => {
          console.log(error)
        }).then( () => {
          this.loading = false
        })
      },
    },
    mounted() {
      this.fetchFullnames()
    }
  }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>