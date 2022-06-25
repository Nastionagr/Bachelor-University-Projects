<template>
  <div class="wrapper">
    <div class="header-content">
      <p class="big"><b>WOUND IMAGE PROCESSING</b></p>
      <p class="small">by</p>
      <p class="small">Anastasiia Solomiia Hrytsyna</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "Header-global"
}
</script>

<style scoped>

  @import url('https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;700&display=swap');

  .wrapper {
    background-color: rgb(127,23,22);
    margin: 0;
    min-height: 110px;
    padding-top: 10px;
    padding-bottom: 10px;
    color: white;
  }

  .header-content{
    padding-top: 15px;
    margin: 0 auto;
    width: 50%;
    text-align: center;
    font-family: 'Source Sans Pro', sans-serif;
  }

  .big {
    margin: 0;
    padding: 0;
    font-weight: 700;
    font-size: 30px;
  }

  .small {
    margin: 0;
    font-weight: 700;
  }
</style>