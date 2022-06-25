<template>
  <div class="header">
    <Header />
  </div>
  <div class="main-content">
    <div class="left">
      <Image @passToChart="this.passToChart" />
    </div>
    <div class="right">
      <PieChart :width="innerWidth" :data-input="chartData" />
	</div>
  </div>
</template>

<script>

import Header from "@/components/Header";
import Image from "@/components/Image";
import PieChart from "@/components/PieChart";

document.title = "Bachelor Thesis"

export default {
  name: 'App',
  components: {
    PieChart,
    Header, Image
  },
  data() {
    return {
      chartData: [100,100,100],
      innerWidth : window.innerWidth*0.4,
	}
  },
  methods: {
    passToChart (event) {
      this.chartData = event
    },
    onResize() {
      this.innerWidth = Math.round(window.innerWidth*0.4)
    }
  },
  mounted() {
    this.$nextTick(() => {
      window.addEventListener('resize',this.onResize)
    })
  },
}
</script>

<style>
  body {
    margin: 0;
  }

  .main-content {
    width: 80%;
    margin: 10px auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    height: 20%;
  }

  .left {
    padding: 5px;
  }

  .right {
    position: relative;
    padding: 5px;
  }
</style>
