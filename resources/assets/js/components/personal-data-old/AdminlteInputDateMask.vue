<!-- Vue component -->
<template>
    <div class="input-group has-feedback" :class="{ 'has-error': hasError }">
        <slot name="label">
            <label :for="this.id">Date</label>
        </slot>
        <transition name="fade">
            <span class="help-block" v-if="hasError" v-text="error"></span>
        </transition>

        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>

            <input @change="onChange" ref="inputDate" type="text" class="form-control" :data-inputmask="inputMask" data-mask="" :id="this.id" :name="this.name"
                   v-model="localeDate" :disabled="disabled">
        </div>
    </div>
</template>

<script>

  import moment from 'moment';
  import Inputmask from "inputmask/dist/inputmask/inputmask.date.extensions";

  export default {
    data () {
      return {
        hasError: false,
        error: '',
        internalDate: this.date,
        newDate: null,
        inputMask: "'alias': '" + window.acacha_relationships.config.dateMask + "'"
      }
    },
    props: {
      disabled: {
        type: Boolean,
        default: false
      },
      date: {
        type: String,
        required: false
      },
      id: {
        type: String,
        default: 'date'
      },
      name: {
        type: String,
        default: 'date'
      }
    },
    watch: {
      date: function(newVal) {
        this.internalDate = newVal
      }
    },
    computed: {
      localeDate: {
        get: function () {
          return moment.utc(this.internalDate).format(window.acacha_relationships.config.momentDateFormat)
        },
        set: function (newValue) {
          this.newDate = moment.utc(newValue, window.acacha_relationships.config.momentDateFormat).
                           format(window.acacha_relationships.config.laravelDateFormat)
        }
      },
    },
    methods: {
      onChange() {
        this.$emit('change',this.newDate)
      }
    },
    mounted() {
      Inputmask().mask(this.$refs.inputDate);
    }
  }

</script>

<style>

</style>