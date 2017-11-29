<template>
    <div class="register-box">

        <div class="register-box-body" id="error">
            <p class="login-box-msg">PROVA</p>

            <form method="post" @submit.prevent="submit">
                <input type="text" class="form-control" placeholder="Your givenName here" name="givenName" value=""
                       :value="form.givenName" @keyup.stop="updateField('givenName', $event.target.value)" autofocus/>

                <div class="row">
                    <div class="col-xs-7">

                    </div>
                    <div class="col-xs-4 col-xs-push-1">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" >Register</button>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.form-box -->
    </div>
</template>

<script>
  import store from './acacha-forms/store';

  export default {
    store,
    data() {
      return {
        form : this.$store.state.form
      }
    },
    methods: {
      submit() {
        this.$store.dispatch('post')
      },
      updateField(field, value) {
        this.$store.commit('updateForm', {field, value});
      },
    },
    mounted() {
      console.log('Component mounted hello.')
    }
  }
</script>