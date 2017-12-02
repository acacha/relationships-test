<template>
    <div class="form-group">
        <label for="identifier">Identifier</label>
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">     <i class="fa fa-spinner fa-spin" v-if="loading"></i>
                    {{ currentIdentifierType.name }}
                    <span class="fa fa-caret-down"></span></button>
                <ul class="dropdown-menu">
                    <li v-for="identifierType in identifierTypes" @click="selectIdentifierType(identifierType)"><a href="#">{{ identifierType.name }}</a></li>
                </ul>
            </div>
            <input type="text" class="form-control" id="identifier" placeholder="Identifier" autocomplete="off"
                   v-model="query"
                   @keydown.down="down"
                   @keydown.up="up"
                   @keydown.enter="hit"
                   @keydown.esc="reset"
                   @blur="reset"
                   @input="update">
        </div>
        <ul v-show="hasItems">
            <li v-for="(item, index) in items" :class="activeClass(index)" @mousedown="hit" @mousemove="setActive(index)">
                <span v-text="item.value"></span>
            </li>
        </ul>
        {{ items }}
    </div>
</template>

<script>

//  IMPORTANT:  CODE NOT COMPLETED PLEASE DO NOT USE

  import Form from 'acacha-forms'
  import VueTypeahead from 'vue-typeahead'
  import axios from 'axios'

  export default {
    extends: VueTypeahead,
    data: function () {
      return {
        identifierTypes: [
          { name : 'DNI'},
          { name : 'Passport'},
          { name : 'NIE'},
        ],
        identifierType : 0,
        form: new Form({ identifier: ''}),
        src: '/api/v1/identifier/search'
      }
    },
    computed: {
      currentIdentifierType() {
        return this.identifierTypes[this.identifierType]
      }
    },
    methods: {
      fetchIdentifierTypes() {
        let url = '/api/v1/identifierType'
        axios.get(url).then((response) => {
          this.identifierTypes = response.data
        }).catch((error) => {
          console.log(error)
        }).then(() => {

        })
      },
      selectIdentifierType(identifierType) {
        this.identifierType = this.identifierTypes.indexOf(identifierType)
      }
    },
    mounted() {
      this.fetchIdentifierTypes()
      this.$http = axios
    }
  }
</script>