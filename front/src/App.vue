<script>
    import Loader from './components/Loader.vue';
    import Header from './components/Header.vue';

    export default {
        name: 'app',
        data: () => ({}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {        
          /*this.user = {
            name: "Marie"
          };*/
        },
        components: {
          Loader,
          Header
        }
    }
</script>

<template>
  <Header/>  
  <router-view></router-view> 
</template>



<style>
:root {
    --primary : #e75112;
    --secondary: #FDAF17;    
    --third: #9B9B9B;
    --loading-secondary: #ff8d5c;
    --loading-fourth: #ffda8f;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F7F7;
    --bg-third: #CCCCCC;
    --bg-navbar: #FFFFFF;
    --bg-navbar-link: #3b3b3b;
    --subtitle: #666;
    --button-bg-disable: #cecece;
    --button-color-disable: #ececec;
    --input-bg: #f2f2f2;
    --input-placeholder:#aaaaaa; 
}

* {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
  --color_foeee: #fff;
}

main {
  min-height: 80vh !important;
  padding:32px;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  min-height: 100%;
  max-width: 100%;
}

body {
	margin: 0;
	min-height: 100vh;
	display: grid;
	grid-template-rows: auto 1fr auto;
  background-image: linear-gradient(12deg, var(--bg-secondary) 0%, var(--bg-third) 100%);
}

main {
  min-height:100%;
  position:relative;
}

.link{
  text-decoration: none;
}

h1 {
  margin-top: 100px !important;
}


/* Button custom */
.btn-custom {
  display: inline-block;
  padding: 0.5rem 0.5rem;
  font-size: 16px;
  font-weight: 700;
  color: var(--bg-navbar-link) !important;
  border: 3px solid var(--primary);
  cursor: pointer;
  position: relative;
  background: var(--bg-navbar);
  text-decoration: none;
  overflow: hidden;
  z-index: 1;
  font-family: inherit;  
  margin-bottom: 20px !important;
}

.btn-custom:hover {
  border: 3px solid transparent;
  background: linear-gradient(var(--bg-navbar), var(--bg-navbar)) padding-box,
      linear-gradient(to right, rgba(231,81,18,0.75), rgba(253,175,23,0.75)) border-box;
}

.btn-custom::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to right, rgba(231,81,18,0.75), rgba(253,175,23,0.75));
  transform: translateX(-100%);
  transition: all .3s;
  z-index: -1;
}

.btn-custom:hover::before {
  transform: translateX(0);
}

.btn-custom:hover {
  color: var(--button-color-disable) !important;
}

.btn-datatable {
  margin-right: 20px;
  border-radius: 5px;
}

/* Button modal custom */
.btn-modal-neutre, .btn-modal-alert, .btn-modal-valid {
  border-radius: 5px;
}

.btn-modal-neutre {
  border: 3px solid rgba(46, 46, 46, 0.75);
}

.btn-modal-neutre:hover {
  border: 3px solid transparent;
  background: linear-gradient(var(--bg-navbar), var(--bg-navbar)) padding-box,
      linear-gradient(to right, rgba(46, 46, 46, 0.75), rgba(112, 112, 112, 0.75)) border-box;
}

.btn-modal-neutre::before {
  background: linear-gradient(to right, rgba(46, 46, 46, 0.75), rgba(112, 112, 112, 0.75));
}

.btn-modal-alert {
  border: 3px solid rgba(204, 0, 0, 0.75);
}

.btn-modal-alert:hover {
  border: 3px solid transparent;
  background: linear-gradient(var(--bg-navbar), var(--bg-navbar)) padding-box,
      linear-gradient(to right, rgba(204, 0, 0, 0.75), rgba(236, 43, 43, 0.75)) border-box;
}

.btn-modal-alert::before {
  background: linear-gradient(to right, rgba(204, 0, 0, 0.75), rgba(236, 43, 43, 0.75));
}

.btn-modal-valid {
  border: 3px solid rgba(4, 146, 28, 0.75);
}

.btn-modal-valid:hover {
  border: 3px solid transparent;
  background: linear-gradient(var(--bg-navbar), var(--bg-navbar)) padding-box,
      linear-gradient(to right, rgba(4, 146, 28, 0.75), rgba(91, 172, 68, 0.75)) border-box;
}

.btn-modal-valid::before {
  background: linear-gradient(to right, rgba(4, 146, 28, 0.75), rgba(91, 172, 68, 0.75));
}

.searchBar {
  outline: none;
  line-height: 26px;
  font-size: 18px;
  margin-right: 20px;
  font-weight: 500;
  color: var(--bg-navbar-link) !important;
  padding: 0.5rem 0.875rem;
  background: linear-gradient(var(--bg-navbar), var(--bg-navbar)) padding-box,
      linear-gradient(to right, rgba(231,81,18,0.75), rgba(253,175,23,0.75)) border-box;
  border: 3px solid transparent;
  border-radius: 0.375rem;
  width: 30%;
  margin-bottom: 20px;
}

.searchBar::placeholder {
  color: var(--bg-navbar-link) !important;
}

.modal-body p {
  font-weight: 600;
  font-size: 18px;
  margin: 0;
}

.modal-title {
  font-weight: 600;
  font-size: 25px;
}

.form-row {
  display: flex;
  margin: 16px 0px;
  gap:16px;
  flex-wrap: wrap;
}

.form-row__input {
  padding:8px;
  border: none;
  border-radius: 8px;
  background: var(--input-bg);
  font-weight: 500;
  font-size: 16px;
  flex:1;
  min-width: 100px;
  color: black;
}

.form-row__input::placeholder {
  color: var(--input-placeholder);
}
</style>
