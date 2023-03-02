<script>
    export default {
        name: 'footer',
        data: () => ({}),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
        }
    }
</script>

<template>
    <footer class="py-3 mt-4">
        <ul class="nav justify-content-center border-bottom pb-3 mb-3">
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Contact</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Mentions légales</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Cookies</a></li>
            <li class="nav-item"><a href="#" class="nav-link px-2 text-muted">Plan d'accès</a></li>
        </ul>
        <p class="text-center text-muted text-copy">Copyright © Nathan Cleaux / Charles Rongier / Axel Dufrenay</p>
    </footer>
</template>

<style scoped>
footer{
  position: absolute;
  width: 100%;
  z-index: 1;
  border: none;    
  padding-top: 1%;
  padding-bottom: 1%;    
  margin-top: 2%;
  bottom: 0;
}

.text-copy {
  font-size: 13px;
}

.footer-text {
  color: #e0dcdc;
}
</style>