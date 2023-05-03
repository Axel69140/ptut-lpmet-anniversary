<script>
    import { accountService } from '../services/account.services';
    import Axios from '../services/caller.services';

    export default {
        name: 'header',
        data: () => ({ user: null, isAdmin: false }),       
        computed: {  
          getFirstName: function() {
            return accountService.getFirstName();
          },
          getLastName: function() {
            return accountService.getLastName();
          }
        },
        mounted() {  
            this.checkIsAdmin();                   
        },
        methods: {  
          logout: async function () {
            accountService.logout();
          },
          checkIsAdmin: function () {
            if (accountService.getToken()) {
              Axios.get(`https://127.0.0.1:8000/users/${accountService.getId()}/role`).then((response) => {
                if (response.data && response.data.role[0] === 'ROLE_ADMIN') {
                  this.isAdmin = true;
                } else {
                  this.isAdmin = false;
                }                         
              });       
            } else {
              this.isAdmin = false;
            }
          }
        }
    }
</script>

<template>
  <header>
    <div class="nav-top">
    </div>

    <nav class="navbar navbar-expand-xxxl">       
      <div class="nav-img">
        <a href="/"><img src="/img/logoIUTBlanc.png" alt="Logo de l'IUT Lyon 1" class="img-nav"></a>
      </div>  

      <div class="navbar-mobile">
        <div class="form-inline my-md-0 display-mobile">
          <a v-if="!this.$store.state.user.token" class="btn-custom btn" href="/login" role="button">Connexion</a>
          <div v-if="this.$store.state.user.token" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/" id="dropdown3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ getFirstName }} {{ getLastName }}</a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown3">
              <a class="dropdown-item" href="" @click="logout()">Déconnexion</a>
              <div v-if="isAdmin">
                <a class="dropdown-item" href="/admin">Gestion administrateur</a>
                <a class="dropdown-item" href="/admin/user">Gestion des utilisateurs</a>
                <a class="dropdown-item" href="/admin/participant">Gestion des participants</a>
                <a class="dropdown-item" href="/admin/timeline">Gestion de la timeline</a>
                <a class="dropdown-item" href="/admin/article">Gestion des news</a>
                <a class="dropdown-item" href="/admin/activity">Gestion des activités</a>
                <a class="dropdown-item" href="/admin/anecdote">Gestion des anecdotes</a>
              </div>
            </div>
          </div>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <div class="collapse navbar-collapse navbar-custom" id="navbar">
        <ul class="navbar-nav mr-auto navbar-ul">
          <li class="nav-item">
            <a class="nav-link" href="/">Accueil</a>            
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/event" id="dropdown0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Événement</a>
            <div class="dropdown-menu" aria-labelledby="dropdown0">
              <a class="dropdown-item" href="/event">Voir l'événement</a>
              <a class="dropdown-item" href="/event/registration">Participer</a>
              <a class="dropdown-item" href="/event/users">Liste des participants</a>
              <a class="dropdown-item" href="/event/activity/form">Proposer une activité</a>    
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/articles" id="dropdown1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">News</a>
            <div class="dropdown-menu" aria-labelledby="dropdown1">
              <a class="dropdown-item" href="/articles">Voir</a>
              <a class="dropdown-item" href="/articles/form">Proposer une news</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/" id="dropdown2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anecdotes</a>
            <div class="dropdown-menu" aria-labelledby="dropdown2">
              <a class="dropdown-item" href="/anecdote/form">Proposer une anecdote </a>
            </div>
          </li>
        </ul>  

        <div class="form-inline my-md-0 display-desktop" :class="{addmargin : !this.$store.state.user.token}">
          <a v-if="!this.$store.state.user.token" class="btn-custom btn" href="/login" role="button">Connexion</a>
          <div v-if="this.$store.state.user.token" class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="/" id="dropdown3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ getFirstName }} {{ getLastName }}</a>   
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown3">
              <a class="dropdown-item" href="" @click="logout()">Déconnexion</a>
              <div v-if="isAdmin">
                <a class="dropdown-item" href="/admin">Gestion administrateur</a>
                <a class="dropdown-item" href="/admin/user">Gestion des utilisateurs</a>
                <a class="dropdown-item" href="/admin/participant">Gestion des participants</a>
                <a class="dropdown-item" href="/admin/timeline">Gestion de la timeline</a>
                <a class="dropdown-item" href="/admin/article">Gestion des news</a>
                <a class="dropdown-item" href="/admin/activity">Gestion des activités</a>
                <a class="dropdown-item" href="/admin/anecdote">Gestion des anecdotes</a>
              </div>
            </div>
          </div>
        </div>      
      </div>
    </nav>
  </header>
</template>

<style scoped>
@media (min-width: 1550px) {  
  .navbar {
    position: absolute;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0 260px !important;
    height: 75px;
    top: 85px;
    width: 100%;
  }
  .display-desktop {
    display: initial;
  }

  .display-mobile {
    display: none;
  }

  .navbar-custom {
    height: 100%;
  }

  .navbar-ul {
    margin-left: 22%;
  }

  .nav-img {
    width: 300px;
    height: 195px;
    line-height: 170px;
    top: -100px;
    left: 150px;
    background-color: var(--primary);
    position: absolute;
    display: flex;
    align-items: center;
    z-index: 100000;
  }  
}

@media (max-width: 1549px) {  
  .navbar {
    position: absolute;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0 260px !important;
    height: 75px;
    top: 85px;
    width: 100%;
  }
  
  .dropdown-item {
    border-top: 1px solid #eceef0 !important;
  }

  .display-desktop {
    display: none;
  }

  .display-mobile {
    display: initial;
  }
  
  .navbar-mobile {
    background-color: var(--bg-navbar);
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }

  .navbar-toggler {
    height: 60%;
    margin: 0 20px;
  }  

  .navbar-ul li {
    border-left: none !important;
  }

  .navbar-ul li a {
    display: flex;
    justify-content: center;
  }

  .nav-img {
    width: 300px;
    height: 195px;
    line-height: 170px;
    top: -100px;
    left: 150px;
    background-color: var(--primary);
    position: absolute;
    display: flex;
    align-items: center;
    z-index: 100000;
  }

  .navbar-custom div {
    margin-right: 0px;
  }
}

@media (max-width: 992px) {
  .nav-top {
    display: none;
  }

  .navbar {
    position: absolute;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0 !important;
    height: 75px;
    top: 0px;
    width: 100%;
  }

  .nav-img {
    width: 250px;
    height: 100px;
    line-height: 170px;
    top: 0px;
    left: 45px;
    background-color: var(--primary);
    position: absolute;
    display: flex;
    align-items: center;
    z-index: 100000;
  }

  .img-nav {
    width: 70% !important;
  }
}

@media (max-width: 576px) {
  .nav-top {
    display: none;
  }

  .navbar {
    position: absolute;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 0 !important;
    height: 75px;
    top: 0px;
    width: 100%;
  }

  .nav-img {
    display: none !important;
  }
}

.navbar-ul li a:active, .dropdown-item:active {
  background-color: var(--bs-dropdown-link-hover-bg);
}

.navbar {  
  z-index: 99;
}

.img-nav {
  width: 90%;
}

.navbar .nav-link:hover, .navbar .nav-link:focus, .dropdown-item:hover, .dropdown-item:focus {
  color: var(--primary) !important;
}

.navbar .nav-link, .dropdown-item {
  font-size: 16px !important;
  font-weight: 700 !important;    
}

.navbar-ul li:not(:first-child) {
  border-left: 1px solid #F4E2D4;
}

.nav-top {
  width: 100%;
  height: 120px;
  background: #292323;
}

.navbar-custom {
  background-color: var(--bg-navbar);
}

.addmargin {
  margin-right: 20px; 
} 

.navbar-ul {
  height: 100%;
}

.navbar-ul li {
  height: 100%;
}

.navbar-ul li a {  
  padding: 27px 35px !important;
  height: 100%;
  display: flex;
  align-items: center;  
  color: var(--bg-navbar-link) !important;
}

.form-inline .nav-link, .form-inline .dropdown-item {
  padding: 27px 35px !important;
}

.form-inline .dropdown-menu.show {
  top: 97% !important;
}

.btn-custom {
  display: inline-block;
  padding: 0.9rem 1.8rem;
  font-size: 16px;
  font-weight: 700;
  color: var(--bg-navbar-link) !important;
  border: 3px solid var(--primary);
  cursor: pointer;
  position: relative;
  background-color: transparent;
  text-decoration: none;
  overflow: hidden;
  z-index: 1;
  font-family: inherit;
  margin-bottom: 0px !important;
}

.btn-custom::before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: var(--primary);
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

.dropdown-menu {
  border-radius: inherit;
  border: none;
  top: 98% !important;
  padding: 0 !important;
}

.dropdown-item {
  padding-top: 12px;
  border-bottom: 1px solid #eceef0;
  padding-bottom: 12px;
}

@media (min-width: 1550px) {
  .navbar-expand-xxxl {
    flex-flow: row nowrap;
    justify-content: flex-start;
  }

  .navbar-expand-xxxl .navbar-collapse {
    display: flex !important;
    flex-basis: auto !important;
  }

  .navbar-expand-xxxl .navbar-nav {
    flex-direction: row;
  }
}
</style>