<script>
    import axios from 'axios';  
    import Footer from '../../components/Footer.vue';  

    export default {
        name: 'mentions-legales',
        data: () => ({ users: [] }),       
        computed: {            
        },
        methods: {      
        },
        mounted() {       
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Mentions légales</h1> 
        
        <div class="container mt-5">
            <section>
                <h2>Propriétaire du site</h2>
                <p>///</p>
            </section>  

            <section>
                <h2>Conception et création du site</h2>
                <p>Nathan Cleaux / Charles Rongier / Axel Dufrenay</p>
            </section>  

            <section>
                <h2>Hébergement</h2>
                <p>
                    Direction du Système d'Information <br>
                    Université Claude Bernard Lyon 1 <br>
                    43 bd du 11 Novembre 1918 <br>
                    69100 VILLEURBANNE
                </p>
            </section>  

            <section>
                <h2>Informations personnelles</h2>
                <p>
                    L'Université Claude Bernard Lyon 1 s’engage à ne pas divulguer les informations personnelles vous concernant à des tiers.
                </p>
                <p>
                    L'Université Claude Bernard Lyon 1 assure la sécurité de vos données transmises par vos soins mais vous demande de prendre les précautions nécessaires 
                    quant aux risques inhérents de l’outil Internet.
                </p>
                <p>
                    L'Université Claude Bernard Lyon 1 à ce que la collecte et le traitement de vos données, effectués à partir de ce site, soient conformes au règlement général 
                    sur la protection des données (RGPD) en date du 25 mai 2018 et à la loi Informatique et Libertés.
                </p>
                <p>
                    Conformément à la Loi informatique et libertés du 6 janvier 1978, modifiée en 2004, vous disposez d’un droit d’accès, de modification, de rectification et de 
                    suppression des données vous concernant. Pour exercer ce droit, nous vous invitons à nous contacter par courrier à l’adresse ci-dessus.
                </p>
            </section> 

            <section>
                <h2>Droit d'auteur</h2>
                <p>
                    Toutes les données de ce site (textes, images, illustrations, photos, squelettes techniques des pages) sont protégées par la législation sur le droit d’auteur. 
                    Il est donc interdit d’exploiter ce site sans autorisation signée de l'Université Claude Bernard Lyon 1.
                </p>
                <p>
                    A ce titre, toute reproduction, représentation, utilisation, adaptation, modification, traduction, commercialisation, partielles ou intégrales par quelque 
                    procédé et sur quelque support que ce soit est interdite, sans accord préalable de l'Université Claude Bernard Lyon 1.
                </p>
            </section> 
        </div>
    </main>

    <Footer class="footer"/>
</template>

<style scoped>
    .footer {
        position: inherit;
    }

    h1, h2 {
        font-weight: 600;    
    }

    h2, p {
        text-align: initial;
    }

    p {
        font-size: 1.2rem;
        margin-bottom: 24px;
    }
    .container {
        width: 70%;
    }
</style>