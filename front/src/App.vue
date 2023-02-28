<template>
  <header>
  </header>
  
  <main>
    <div>
      <router-view></router-view>
    </div>
  </main>

 <footer class="py-3 mt-4">
    <p class="text-center footer-text">Copyright © Nathan Cleaux / Charles Rongier / Axel Dufrenay</p>
</footer>
</template>



<style>
* {
  padding: 0;
  margin: 0;
  --color_foeee: #fff;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  min-height: 100%;
}

body {
	margin: 0;
	min-height: 100vh;
	display: grid;
	grid-template-rows: auto 1fr auto;
  background-color: #f0f0f0 !important;
}

main {
    min-height:100%;
    position:relative;
}

footer{
  position: absolute;
  width: 100%;
  z-index: 1;
  border: none;    
  color: rgb(255, 255, 255) !important;
  letter-spacing: 2px;
  background-color: #2c3e50 !important;
  padding-top: 1%;
  padding-bottom: 1%;    
  margin-top: 2%;
  font-family: 'Mukta', sans-serif;   
  bottom: 0;
}

.link{
  text-decoration: none;
}

.footer-text {
  color: #e0dcdc;
}
</style>
