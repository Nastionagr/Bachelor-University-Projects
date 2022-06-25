<template>
  <SpinnerData id="SpinnerData" v-show="apiFinished===false"/>

<div class="bg-image">
  <img class="output" v-if="url" :src="url"/>
  <img class="overlay" v-if="url&&maskToggle" :src="mask" />
</div>
  <label v-show="!url" class="uploader">
    <img class="upload-icon" src="../assets/upload.png"/>
    <input type="file" accept="image/jpeg, image/png, image/jpg" @change="onFileChange"/>
  </label>
  <div class="controls" v-if="url">
    <button class="button" v-show="response===null" @click="sendRequest">analyze</button>
    <button class="button" v-show="response!==null" @click="downloadMask">save mask</button>
    <button class="button"  @click="processImage">mask toggle</button>
    <button class="button" @click="resetState('reset')">reset</button>
  </div>
</template>

<script>
import SpinnerData from "@/components/Spinner";
import imageToBase64 from 'image-to-base64/browser';

export default {
  name: "Image-left",
  components: {SpinnerData},
  data() {
    return {
      uploaded: false,
      url: null,
      apiFinished: true,
      image64: null,
      response: null,
      mask: null,
      maskToggle: false,
    }
  },
  methods: {
    onFileChange(e) {
      const file = e.target.files[0] || e.dataTransfer.files;
      this.url = URL.createObjectURL(file)
      this.image = this.url
      imageToBase64(this.url)
          .then(
              (response) => {
                this.image64 = response
              }
          )
          .catch(
              (error) => {
                this.resetState(error)
              }
          )
    },
    resetState(error) {
      this.url = null;
      if (error !== "reset") {
        this.apiFinished = true
        this.response = null
        this.$toast.show(error, {
          type: 'error',
          position: 'top',
          duration: 10000
        });
      } else {
        this.apiFinished = true
        this.$toast.show("Reset!", {
          position: 'top',
          duration: 1000
        });
        this.$emit('passToChart', ['100', '100', '100'])
        this.response = null
        this.mask = null
        this.maskToggle = false
      }
    },
    sendRequest() {
      this.apiFinished = false

      const axios = require('axios')

      axios
          .post('http://127.0.0.1:2200/analyze', {
            "url": this.image64
          })
          .then(response => (this.response = response.data))
          .catch(e => (console.log(e)));

      setTimeout(() => this.dataIn(), 7500)
      setTimeout(() => this.apiFinished = true, 7500)
    },
    dataIn() {
      this.$emit('passToChart', [this.response['red'], this.response['green'], this.response['blue']])
      // this.$emit('passToChart')
    },
    processImage() {
      if (this.response !== null) {
        this.mask = 'data:image/jpeg;base64,' + this.response['mask'].split("'")[1]
        this.maskToggle = !this.maskToggle
      }
    },
    downloadMask() {
      const curTime = Math.round(+new Date()/1000);
      const image = 'data:image/jpeg;base64,' + this.response['mask'].split("'")[1]
      const link = document.createElement("a")
      link.href = image
      link.download = curTime
      link.click()
    }
  },
  emits: ['passToChart']
}
</script>

<style scoped>
.output {
  width: 100%;
  position: relative;
}

input[type="file"] {
  display: none;
}

.uploader {
  border: 1px solid #ccc;
  display: inline-block;
  padding: 20px 20px;
  cursor: pointer;
  margin: 0 auto;
}

.button {
  display: inline-block;
  border: 1px solid rgb(127, 23, 22);
  min-width: 32%;
  font-size: 18px;
  color: white;
  background-color: rgb(127, 23, 22);
  padding: 5px;
  margin-right: 2px;
  margin-top: 25px;
}

.button:hover {
  border: 1px solid rgb(127, 23, 22);
  background-color: white;
  color: rgb(127, 23, 22);
  cursor: pointer;
}

.controls {
  margin: 0 auto;
}

.upload-icon {
  width: 100%;
  min-width: 50px;
}

#SpinnerData {
  position: absolute;
  left: 49%;
}

.bg-image {
  position: relative;
  overflow: hidden;
}
.overlay {
  position: absolute;
  opacity: 75%;
  top: 0;
  left: 0;
  width: 100%;
}
</style>
