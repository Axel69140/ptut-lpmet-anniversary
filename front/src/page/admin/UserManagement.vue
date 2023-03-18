<script lang="ts" setup>
    import { defineComponent } from 'vue';
    import { Header, Item } from "vue3-easy-data-table";
    import { onMounted, ref } from "vue";
    import axios from "axios";
    import Footer from '../../components/Footer.vue';    

    const searchValue = ref('');

    const users = ref([]);

    const headers: Header[] = [
        { text: "Prénom", value: "firstName" },
        { text: "Nom", value: "lastName" },
        { text: "Nom de jeune fille", value: "maidenName" },
        { text: "Email", value: "email" },
        { text: "Numéro de téléphone", value: "phoneNumber" },
        { text: "Année d'activité", value: "activeYears" },
        { text: "Fonction", value: "function" },
        { text: "Lien linkedIn", value: "link" },
        { text: "Note", value: "note" },
        { text: "Participe à l'évènement", value: "participated" },
        { text: "Profil publique", value: "publicProfil" }
    ];

    const items: Item[] = users.value;

    onMounted(async () => {
        axios.get('https://127.0.0.1:8000/users').then((response) => {
            console.log(response.data);
            users.value.push(...response.data);            
        }); 
    });    
    
    defineComponent({
        name: 'userManagement',
        components: {
            Footer
        }
    });
</script>


<template>
    <main>
        <h1>Gestion des utilisateurs</h1>    

        <div class="container">      
            <div id="function-datatable">
                <input type="text" placeholder="Rechercher..." v-model="searchValue">
            </div>

            <EasyDataTable
                :headers="headers"
                :items="items"
                :search-value="searchValue"
                alternating
                border-cell
                buttons-pagination
                rows-per-page-message="Ligne par page"
            >
                <template #item-activeYears="item">
                    {{ item.activeYears.length > 0 ? item.activeYears[0] + "/" + item.activeYears[1] : "" }}                    
                </template>

                <template #item-participated="item">
                    {{ item.participated === true ? "Oui" : "Non" }}                    
                </template>

                <template #item-publicProfil="item">
                    {{ item.publicProfil === true ? "Oui" : "Non" }}                    
                </template>
            </EasyDataTable>
        </div>
    </main>

    <Footer/>
</template>

<style scoped>
h1 {
    margin-bottom: 3.5rem;
}

#function-datatable {
    margin-bottom: 20px;
    display: flex;
}
</style>