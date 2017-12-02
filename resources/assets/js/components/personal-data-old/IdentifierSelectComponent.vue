<template>
    <div class="form-group has-feedback" :class="{ 'has-error': hasError }">
        <transition name="fade">
            <label class="help-block" v-if="hasError" v-text="error"></label>
            <slot name="label" v-else>
                <label>Identifier</label>
            </slot>
        </transition>
        <div class="input-group">
            <div class="input-group-btn">
                <button id="identifierType" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                    {{ identifierTypeName }}
                    <span class="fa fa-caret-down"></span>
                </button>
                <ul class="dropdown-menu">
                    <li v-for="identifierType in identifierTypes" @click="selectIdentifierType(identifierType)"><a href="#">{{ identifierType.name }}</a></li>
                </ul>
            </div>
            <multiselect id="identifier" v-model="internalIdentifier" :options="identifiers" :custom-label="customLabel"
                         :taggable="true" @tag="addIdentifier" @select="identifierHasBeenSelected"
                         placeholder="Select identifier"
                         tag-placeholder="Add this as new identifier"
                         :disabled="disabled" :loading="loading"></multiselect>
        </div>
    </div>

</template>

<script>

  import Multiselect from 'vue-multiselect'
  import axios from 'axios'

  export default {
    components: { Multiselect },
    data () {
      return {
        internalIdentifier: null,
        loading: false,
        identifiers: [],
        identifierType : null,
        identifierTypes: []
      }
    },
    computed: {
      identifierTypeName() {
        return this.identifierType ? this.identifierType.name : ''
      },
      hasError() {
        return this.error ? true : false
      }
    },
    props: {
      selected: {
        type: String,
        required: false
      },
      disabled: {
        type: Boolean,
        default: false
      },
      identifier: {
        default: null
      },
      type: {
        default: null
      },
      error: {
        default: null
      }
    },
    watch: {
      identifier: function(newVal, oldVal) {
        console.log('watcher for identifier')
        this.updateIdentifier(newVal)
      },
      type: function(newVal,oldVal) {
        console.log('watcher for type')
        this.updateIdentifierType(newVal)
      }
    },
    methods: {
      updateIdentifier(identifier) {
        console.log('updateIdentifier')
        console.log('indentifier:')
        console.log(identifier)
        this.internalIdentifier = this.identifiers[identifier]
        if (this.internalIdentifier) this.updateIdentifierType(this.internalIdentifier.type_id)
      },
      updateIdentifierType(id) {
        this.identifierType = this.findIdentifierType(id)
      },
      findIdentifierType(id) {
        return this.identifierTypes.find((identifierType) => {
          return identifierType.id === id
        })
      },
      identifierHasBeenSelected(identifier) {
        this.updateIdentifierType(identifier.type_id)
        this.$emit('selected',identifier)
      },
      customLabel({ value, type_name}) {
        return `${value} - ${type_name}`
      },
      fetchIdentifierTypes() {
        let url = '/api/v1/identifierType'
        this.loading = true
        axios.get(url).then((response) => {
          this.identifierTypes = response.data
          this.setDefaultSelectedIdentifierType()
        }).catch((error) => {
          console.log(error)
        }).then(() => {
          this.loading = false
        })
      },
      selectIdentifierType(identifierType) {
        this.identifierType = identifierType
        if (this.internalIdentifier) {
          this.internalIdentifier.type = identifierType
          this.internalIdentifier.type_id = identifierType.id
          this.internalIdentifier.type_name = identifierType.name

          let id = this.identifiers.find((identifier) => {
            return identifier.id === this.internalIdentifier.id
          })

          id.type = identifierType
          id.type_id = identifierType.id
          id.type_name = identifierType.name
        }
      },
      fetchIdentifiers() {
        let url = '/api/v1/identifier'
        axios.get(url).then((response) => {
          this.identifiers = response.data
        }).catch((error) => {
          console.log(error)
        })
      },
      addIdentifier(newIdentifier) {
        const identifier = {
          value: newIdentifier,
          type_id: this.identifierType.id,
          type: this.identifierType,
          type_name: this.identifierType.name
        }
        this.identifiers.push(identifier)
        this.internalIdentifier = identifier
        this.$emit('tag', identifier)
      },
      setDefaultSelectedIdentifierType() {
        if ( this.selected ) {
          this.identifierType = this.identifierTypes.find((identifierType) => {
            return identifierType.name === this.selected
          })
          if (this.identifierType !== undefined) return
        }
        this.identifierType = this.identifierTypes[0];
      }
    },
    mounted() {
      this.fetchIdentifierTypes()
      this.fetchIdentifiers()
    }
  }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

</style>