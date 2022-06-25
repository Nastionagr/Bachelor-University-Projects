<template>
  <Bar
    :chart-data="chartData"
    :chart-options="options"
    :width="width"
  />
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
export default {
  name: "PieChart",
  components: {
    Bar,
  },
  props: {
    dataInput: {
      type: Array,
      default: () => [100,100,100],
    },
    width: Number
  },
  data() {
    return {
      options: {
        responsive: true,
        aspectRatio: 1,
		backgroundColor: ['red','green','blue']
      },
      datasetIdKey: "TEST"
    }
  },
  computed: {
    chartData() {
      return {
        labels: ['granulation tissue','slough tissue','necrotic tissue'],
        datasets: [{ 
				label: "Results of tissue segmentation",
				data:this.dataInput, 
		}],
      }
    }
  }
}
</script>

<style scoped>

</style>
