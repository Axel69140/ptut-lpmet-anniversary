<template>
  <header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">Expand at lg</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample05">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown05" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown05">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
        <form class="form-inline my-2 my-md-0">
          <input class="form-control" type="text" placeholder="Search">
        </form>
      </div>
    </nav>
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
:root {
    --primary : #e75112;
    --secondary: #FDAF17;    
    --third: #9B9B9B;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F7F7;
    --bg-third: #CCCCCC;
    --bg-navbar: #F1F1F1;
}

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

.navbar {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem !important;
}

.link{
  text-decoration: none;
}

.footer-text {
  color: #e0dcdc;
}
</style>
