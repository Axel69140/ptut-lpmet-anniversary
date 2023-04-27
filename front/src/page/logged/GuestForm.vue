<script>
    import Footer from '../../components/Footer.vue';     
    import { accountService } from '../../services/account.services';
    //import { guestService } from '../../services/guest.services';

    export default {
        name: 'guestForm',
        data: () => ({ 
            users: [],
            idUser: 1,
            email: '',
            name: '',
        }),       
        computed: {            
        },
        methods: {     
            async saveGuest() {
                if (this.name !== undefined && this.name !== '' && this.email !== undefined && this.email !== '') {
                   /* await guestService.createGuest({
                        name: this.name,
                        email: this.email,
                        invitedBy: this.idUser,
                    });*/

                    this.name = '';
                    this.email = '';
                    alert("Merci de votre contribution. Votre invité(e) à bien été pris en compte.");
                    this.$router.push('../event');
                }
                else{
                    alert('Il manque des informations');
                }
            }
        },
        async mounted() {
            this.idUser = await accountService.getId();
        },
        components: {
            Footer
        }
    }
</script>


<template>
    <main>
        <h1>Ajouter un(e) invité(e)</h1>
        <p class="informations"></p>
        <div class="formulaireInvite">
            <div class="divNameTZ">
                <label class="labelName">Nom</label>
                <input type="text" class="nameTextZone" v-model="name">
            </div>
            <div class="divEmailTZ">
                <label class="labelEmail">Email</label>
                <input type="text" class="emailTextZone"  v-model="email">
            </div>
            <div class="sendButton">
                <button @click="saveGuest()" class="btn-custom">Ajouter l'invité(e)</button>
            </div>
        </div>
    </main>
    
    <Footer class="footer" />
</template>

<style scoped>
.uploadImage{
    border: solid 3px black;
}
label.labelImage{
    cursor: pointer;
    border: dashed 3px var(--primary);
    background-color: #fff;
    border-radius: 20px;
    padding: 0.5%;
    width: 50%;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
}
input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
}
.image-preview{
    margin: 15px;
    display: flex;
    justify-content: center;
}
.imagePreview{
    max-width: 50%;
    max-height: 80%;
    margin: 2%;
    background-color: red;
}
.yesImage{
    width: 50%;
    height: auto;
    background-color: #fff;
}
.noImage{
    
    
    background-color: #fff;
}
.labelEmail{
    margin:  20px 15px 0 0;
}

.labelName{
    margin-right: 28px;
}


.formulaireArticle{
    display: flex;
    flex-direction: column;
}

.divImage{
    display: flex;
    justify-content: center;
}
.divnameTZ{
    display: flex;
    justify-content: center;
    align-items: center;
}
.divEmailTZ{
    display: flex;
    justify-content: center;
}
.footer{
    position: inherit;
}
.divEmailTZ{
    margin: 20px 0;
}
.emailTextZone, .nameTextZone{
    width: 60%;
    border-radius: 20px;
    padding: 1%;
    border: solid 4px var(--primary);
}

.emailTextZone:focus-visible, .nameTextZone:focus-visible{
    outline: var(--primary);
}


</style>