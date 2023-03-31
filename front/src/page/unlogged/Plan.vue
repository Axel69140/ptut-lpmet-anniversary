<script>
    import axios from 'axios';  
    import Footer from '../../components/Footer.vue';  
    import { router } from '../../routes';

    export default {
        name: 'plan',
        data: () => ({ users: [], routes : [] }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {  
            let allRoutes = router.getRoutes();  
            this.routes.push(... allRoutes);        
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Plan d'accès</h1>    
        
        <div class="container mt-5">
            <h2>Pages</h2>
            <ul>
                <template v-for="route in routes" v-bind:key="route">
                    <li v-if="route.name && route.name !== 'All'">
                        <a  v-bind:href="route.path">{{ route.name }}</a>
                    </li>
                </template>
            </ul>
        </div>
    </main>

    <Footer/>
</template>

<style scoped>
    h1, h2 {
        font-weight: 600;    
    }

    h2, p, li {
        text-align: initial;
    }

    p {
        font-size: 1.2rem;
        margin-bottom: 24px;
    }
    .container {
        width: 60%;
    }

    a {
        font-size: 20px;
        text-decoration: none;
        color: var(--primary)
    }

    a:hover {
        text-decoration: none;
        color: var(--bg-navbar-link)
    }
</style>