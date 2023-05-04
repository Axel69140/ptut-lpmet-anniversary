<script>
    import Footer from '../../components/Footer.vue';     
    import { accountService } from '../../services/account.services';
    import { guestService } from '../../services/guest.service';

    export default {
        name: 'guestForm',
        data: () => ({ 
            users: [],
            idUser: 1,
            email: '',
            firstName: '',
            lastName: '',
        }),       
        computed: {            
        },
        methods: {     
            async saveGuest() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.email && !emailRegex.test(this.email)) {
                    alert('Email invalide');
                } else {
                    if (this.firstName !== undefined && this.firstName !== '' && this.email !== undefined && this.email !== '' && this.lastName !== undefined && this.lastName !== '') {
                    await guestService.createGuest({
                        firstName: this.firstName,
                        lastName: this.lastName,
                        email: this.email,
                        invitedBy: this.idUser,
                    });

                    this.firstName = '';
                    this.lastName = ' ';
                    this.email = '';
                    alert("Merci de votre contribution. Votre invité(e) à bien été pris en compte.");
                    this.$router.push('../event');
                    }
                    else{
                        alert('Il manque des informations');
                    }
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
                <label class="labelFirstName">Prénom</label>
                <input type="text" class="nameTextZone" v-model="firstName">
            </div>
            <div class="divNameTZ">
                <label class="labelName">Nom</label>
                <input type="text" class="nameTextZone" v-model="lastName">
            </div>
            <div class="divEmailTZ">
                <label class="labelEmail">Email</label>
                <input type="email" class="emailTextZone"  v-model="email">
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

.labelFirstName{
    margin-right: 10px;
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
    margin:  20px 25px 0 0;
}

.labelName{
    margin-right: 38px;
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
.emailTextZone, .nameTextZone{
    width: 60%;
    border-radius: 20px;
    padding: 1%;
    margin: 20px;
    border: solid 4px var(--primary);
}

.emailTextZone:focus-visible, .nameTextZone:focus-visible{
    outline: var(--primary);
}


</style>