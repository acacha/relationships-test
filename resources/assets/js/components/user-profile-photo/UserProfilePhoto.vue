<template>
    <div>

        <img ref='photoImage' class="profile-user-img img-responsive img-circle" :src="photo" alt="User photo" @click="upload" >

        <h3 class="profile-username text-center" @click="upload">{{username}}</h3>

        <form role="form" class="form" onsubmit="return false;">
            <div class="btn btn-primary btn-block image-upload" :disabled="isSaving">
                <label for="file-input">
                    <i class="fa fa-refresh fa-spin fa-lg" v-if="isSaving"></i>
                    <i class="fa fa-check text-green fa-lg" v-if="isSuccess"></i>
                    <i class="fa fa-close text-red fa-lg" v-if="isFailed"></i>
                    <b>
                        <template v-if="isInitial">Upload photo</template>
                        <template v-if="isSaving">Uploading photo</template>
                        <template v-if="isSuccess">Photo uploaded</template>
                        <template v-if="isFailed">Error uploading</template>
                    </b>
                </label>
                <input type="file" name="photo" id="file-input" ref="photo" accept="image/*" :disabled="isSaving"
                       @change="filesChange"/>

            </div>
        </form>
    </div>
</template>

<style>
    .image-upload > input
    {
        display: none;
    }
</style>

<script>

  import malePhoto from "./assets/defaultmale.png"
  import femalePhoto from "./assets/defaultfemale.png"

  const STATUS_INITIAL = 0, STATUS_SAVING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3

  function wait(ms) {
    return (x) => {
      return new Promise(resolve => setTimeout(() => resolve(x), ms));
    };
  }

  export default {
    data() {
      return {
        username: 'Click to upload',
        photo: malePhoto,
        currentStatus: null
      }
    },
    computed: {
      isInitial() {
        return this.currentStatus === STATUS_INITIAL
      },
      isSaving() {
        return this.currentStatus === STATUS_SAVING
      },
      isSuccess() {
        return this.currentStatus === STATUS_SUCCESS
      },
      isFailed() {
        return this.currentStatus === STATUS_FAILED
      }
    },
    props: {
      gender: {
        validator: function (value) {
          return ['male','female'].includes(value)
        },
        default: 'male'
      }
    },
    mounted() {
      this.reset()
      this.setInitialPhoto()
    },
    methods: {
      setInitialPhoto() {
        if (this.gender === 'female') this.photo = femalePhoto
      },
      upload() {
        this.$refs.photo.click()
      },
      filesChange() {
        // handle file changes
        const formData = new FormData()
        formData.append('photo', this.$refs.photo.files[0])

        //Preview it
        this.preview()

        // save it
        this.save(formData)
      },
      watch: {
        // whenever question changes, this function will run
        status: function (newStatus) {
          this.answer = 'Waiting for you to stop typing...'
          this.getAnswer()
        }
      },
      preview() {
        if (this.$refs.photo.files && this.$refs.photo.files[0]) {
          var reader = new FileReader();
          var component = this
          reader.onload = function (e) {
            component.$refs.photoImage.setAttribute('src', e.target.result);
          }

          reader.readAsDataURL(this.$refs.photo.files[0]);
        }
      },
      save(formData) {
        console.log(formData)
        // upload data to the server
        this.currentStatus = STATUS_SAVING
//        const url = `${BASE_URL}/photos/upload`
        const url = `/upload`
        var component = this

        var config = {
          onUploadProgress: function(progressEvent) {
            var percentCompleted = Math.round( (progressEvent.loaded * 100) / progressEvent.total )
          }
        }

        axios.post(url, formData, config)
          .then(wait(1000))
          .then(function (res) {
            console.log('Done!')
            component.currentStatus = STATUS_SUCCESS
          })
          .catch(function (error) {
            console.log('EEEEEEEEEError!')
            console.log(error)
            component.currentStatus = STATUS_FAILED
          })
      },
      reset() {
        // reset form to initial state
        this.currentStatus = STATUS_INITIAL
        this.uploadError = null
      },
    }
  }
</script>
