<script>  
    import Footer from '../../components/Footer.vue';     
    import { saveAs } from 'file-saver';
    import axios from 'axios';
    import { accountService } from '../../services/account.services';
    import { articleService } from '../../services/article.services';

    export default {
        name: 'articleForm',
        data: () => ({ 
            users: [],
            idUser: 1,
            content: '',
            title: '', 
            imageUrl: null,
            username: '',
            email: '',
            file: null

        }),       
        computed: {            
        },
        methods: {     
            async parameterArticle() {
                if (this.content !== undefined && this.content !== '' && this.title !== undefined && this.title !== '') {
                    await articleService.createArticle({
                        content: this.content,
                        title: this.title,
                        creator: this.idUser,
                    });
                    this.saveImage();
                    this.content = '';
                    this.title = '';
                    alert("Merci de votre contribution. Votre article à bien été pris en compte, après sa validation il apparaitra sur le site.");
                    //this.$router.push('../event');
                }
            },

            async saveImage() {
                const formData = new FormData();
                formData.append('username', this.username);
                formData.append('email', this.email);
                formData.append('file', this.file);

                axios.post('/medias/create', formData).then(response => {
                    console.log("test");
                });
            },

            previewImage(event, isDrop) {
                let file = null;
                if(isDrop){
                    file = event.dataTransfer.files[0];
                }else{
                    file = event.target.files[0];
                }
                console.log(file);
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.imageUrl = event.target.result;
                };
                reader.readAsDataURL(file);
                this.file = event.target.files[0];
            },
            dropHandler(event) {
                event.preventDefault();
                const file = event.dataTransfer.files[0];
                console.log(file);
                this.previewImage(event,true);
            },
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
        <h1>Proposer une news</h1>
        <p class="informations"></p>
        <form @submit.prevent="handleSubmit">
            <div class="formulaireArticle">
                <div class="divTitleTZ">
                    <label class="labelTitle">Titre de la news</label>
                    <input type="text" class="titleTextZone" v-model="title">
                </div>
                <div class="divContentTZ">
                    <label class="labelContent">Contenu de la news</label>
                    <textarea class="contentTextZone" rows="10" cols="100" v-model="content"></textarea>
                </div>
                <div class="divImage">
                    <label v-if="!imageUrl" for="imageFile" class="labelImage dropzone" @drop="dropHandler" @dragover.prevent>Ajouter une image à la news</label>
                    <label  v-if="imageUrl" for="imageFile" class="labelImage dropzone" @drop="dropHandler" @dragover.prevent>Changer d'image
                        <img :src="imageUrl" v-if="imageUrl" class="imagePreview">
                    </label>
                    <input class="upload" id="imageFile" name="imageFile" ref="fileInput" type="file" accept=".png, .jpeg, .jpg, .webp" @change="this.previewImage($event,false)">
                </div>
                <div class="sendButton">
                    <button @click="parameterArticle()" type="submit" class="btn-custom">Envoyer la news</button>
                </div>
            </div>
        
        </form>


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
}
.yesImage{
    width: 50%;
    height: auto;
    background-color: #fff;
}
.noImage{
    
    
    background-color: #fff;
}
.labelContent{
    margin:  20px 15px 0 0;
}

.labelTitle{
    margin-right: 48px;
}


.formulaireArticle{
    display: flex;
    flex-direction: column;
}

.divImage{
    display: flex;
    justify-content: center;
}
.divTitleTZ{
    display: flex;
    justify-content: center;
    align-items: center;
}
.divContentTZ{
    display: flex;
    justify-content: center;
}
.footer{
    position: inherit;
}
.divContentTZ{
    margin: 20px 0;
}
.contentTextZone, .titleTextZone{
    width: 60%;
    border-radius: 20px;
    padding: 1%;
    border: solid 4px var(--primary);
}

.contentTextZone:focus-visible, .titleTextZone:focus-visible{
    outline: var(--primary);
}

@media (max-width: 560px) {
    .divTitleTZ, .divContentTZ{
        flex-direction: column;
        align-items: center;
    }

    .labelTitle{
        margin: 0;
    }

    .contentTextZone, .titleTextZone{
        width: 80%;
    }

}

</style>