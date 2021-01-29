<template>
    
        <div class="editor"> 
            <h2>Это админка</h2>          

            <div v-for="(bodyT,index) of bodyText" @key="index" :class="bodyT.hidden">
                <div class="row" v-if="bodyT.type === 'img'">

                    <div class="col">                        
                        
                        <button class="add" type="submit" @click.prevent="addTextareaIn(index)"><i name="bold"></i></button>                    
                        <button class="addImage" type="submit" @click.prevent="FindFile2(index)"><i name="bold"></i></button> 
                        <input type="file"  :id="['my_hidden_file'+index]" class="input input__file" multiple="" accept="image/jpeg,image/png,image/gif" @change.prevent="addImagearea(index)">  

                        <div class="article_body">
                            <div style="width: 660px" class="wp-caption aligncenter">                                                                                     
                                <img width="650px" v-bind:src="bodyT.title" />                                    
                                <input :name="['opisanie[' + index + ']']" v-model="bodyT.opisanie" type="text" :id="['Opisanie' + index + '']" class="wp-caption-textarea" placeholder="Описание">
                                <input :name="['alt[' + index + ']']" v-model="bodyT.alt" type="text" :id="['alt' + index + '']" class="wp-caption-textarea" placeholder="Alt">
                            </div>
                        </div>

                        <button class="delete "type="submit" @click.prevent="deleteImagearea(index,bodyT.title)"><i name="bold"></i></button>
                 
                    </div>                    
                            

                </div> 

                <div v-else class="row" >                    

                    <div class="col">

                        <button class="add" type="submit" @click.prevent="addTextareaIn(index)"><i name="bold"></i></button>
                        <button class="addImage" type="submit" @click.prevent="FindFile2(index)"><i name="bold"></i></button> 
                        <input type="file"  :id="['my_hidden_file'+index]" class="input input__file" multiple="" accept="image/jpeg,image/png,image/gif" @change.prevent="addImagearea(index)">  
  

                        <editor-menu-bubble :editor="editors[index]" :keep-in-bounds="keepInBounds" v-slot="{ commands, isActive, menu }">
                          <div
                            class="menububble"
                            :class="{ 'is-active': menu.isActive }"
                            :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
                          >

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.bold() }"
                              @click.prevent="commands.bold"
                            >
                              <i name="bold" class="fas fa-bold"></i>
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.italic() }"
                              @click.prevent="commands.italic"
                            >
                              <i name="italic" class="fas fa-italic"></i>
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.heading({ level: 1 }) }"
                              @click.prevent="commands.heading({ level: 1 })"
                              :style="`font-weight:bold;`"
                            >
                              H1
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.heading({ level: 2 }) }"
                              @click.prevent="commands.heading({ level: 2 })"
                              :style="`font-weight:bold;`"
                            >
                               H2
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.heading({ level: 3 }) }"
                              @click.prevent="commands.heading({ level: 3 })"
                              :style="`font-weight:bold;`"
                            >
                               H3
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.bullet_list() }"
                              @click.prevent="commands.bullet_list"
                            >
                               <i name="list" class="fas fa-list"></i>
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.ordered_list() }"
                              @click.prevent="commands.ordered_list"
                            >
                              <i name="list-ol" class="fas fa-list-ol"></i>
                            </button>

                            <button
                              class="menububble__button"
                              :class="{ 'is-active': isActive.blockquote() }"
                              @click.prevent="commands.blockquote"
                            >
                              <i name="quote-right" class="fas fa-quote-right"></i>
                            </button>                           

                          </div>
                        </editor-menu-bubble>

                        <editor-content class="editor__content" :editor="editors[index]" :id="index"/>

                        <button class="delete" type="submit" @click.prevent="deleteTextarea(index)"><i name="bold"></i></button>
                    
                    </div>


                </div>

           
            </div>

            <div v-if="fileCurrent != ''" class="progress" style="height: 40px">
                <div class="progress-bar" role="progressbar" :style="{width:fileProgress + '%' }">
                    {{ fileCurrent }}%
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="input__wrapper" @click.prevent="addTextarea()">
                                       
                          <i class="far fa-keyboard"></i></br>
                          <span class="input__file-button-text">Текст</span>                   
                    </div>
                </div>

                <div class="col">
                    <div class="input__wrapper" @click="FindFile()">
                       <input type="file"  id="my_hidden_file" class="input input__file" multiple="" accept="image/jpeg,image/png,image/gif" @change.prevent="addImagearea()">                   
                          <i class="fas fa-camera"></i></br>
                          <span class="input__file-button-text">Картинка</span>                   
                    </div>
                </div>
            </div>


            <div v-for="(bodyT,index) of bodyText" @key="index" class="d-none">
                <input :name="['body[' + index + ']']" type="hidden" :value="bodyT.title"><br>
            </div>

        </div>       
   
</template>

<script>
  
    import { Editor, EditorContent, EditorMenuBubble } from 'tiptap'

    import { 
        HardBreak,     
        Blockquote,
        Heading,
        OrderedList,
        BulletList,
        ListItem,
        Bold,
        Italic,
        Strike,
        Underline,
        History,
    } from 'tiptap-extensions'

    export default {
        props: [
            'items',
            'itemsopis',
            'itemsalt'
        ],

        mounted() {

            if (this.$props.items !== undefined ) {   
            
                var arr = this.$props.items;

                for (const prop in arr) {
                    
                    if (arr[prop].indexOf('public/uploads')) {
                        this.addTextarea(arr[prop])    
                    } else {
                       this.addImageareaProp(arr[prop])
                    }
                }

            } else {
                this.addTextarea();
            }

        },
        
        components: {
            EditorContent,
            EditorMenuBubble,
        },

        data() {
            return {
                
                counter: 0,
                counterEditor:0,

                files0rder: [],
                fileFinish:[],
                fileProgress:0,
                fileCurrent:'',               
                
                bodyText: [],

                keepInBounds: true,
                editors: {},
                
            }
        },

        methods:{

            preProverka() {
                
            },

            FindFile() {
                document.getElementById('my_hidden_file').click();
            },

             FindFile2(index) {
                document.getElementById('my_hidden_file'+index).click();
            },

            addTextarea(textBody){

                this.$set(this.editors,  this.counter,  new Editor({

                    onInit: ({ state, view }) => {

                        const newbodyText = {
                            id: this.counter,
                            title: textBody,
                            completed:false,
                            type: 'text',
                            hidden: 'd-block'
                        }
                        
                        this.bodyText.push(newbodyText);                       

                    },

                    onUpdate: ( { state, getHTML, getJSON, transaction } ) => {

                        var curElement = document.activeElement.parentNode.id;                     

                        this.bodyText[curElement].title = getHTML()

                    },

                    extensions: [  
                        new HardBreak(),                 
                        new Blockquote(),
                        new BulletList(),                        
                        new Heading({ levels: [1, 2, 3] }),                       
                        new ListItem(),
                        new OrderedList(),                        
                        new Bold(),                       
                        new Italic(),                        
                        new Underline(),
                        new History(),                  
                    ],

                    content:textBody

                }));

                this.counter++                  
                                                             
            },

            addTextareaIn(index) {                        

                const newbodyText = {
                    id: index,
                    title: this.bodyText[index].title,
                    completed:false,
                    type: 'text',
                    hidden: 'd-block'
                }


                this.bodyText.splice(index, 0, newbodyText);
                this.$props.itemsopis.splice(index, 0, this.$props.itemsopis[index]);
                this.$props.itemsalt.splice(index, 0, this.$props.itemsalt[index]);

                this.bodyText[index].title = ''
                this.$props.itemsopis[index] = ''
                this.$props.itemsalt[index] = ''

                for (var s in this.editors) {
                    this.editors[s].destroy()
                    delete this.editors[s]   
                }

                for (var i = 0; i < this.bodyText.length; i++) {

                    if (this.bodyText[i].type === 'text') {

                        this.$set(this.editors,  i,  new Editor({

                            onUpdate: ( { state, getHTML, getJSON, transaction } ) => {

                                var curElement = document.activeElement.parentNode.id;                                                   

                                this.bodyText[curElement].title = getHTML()
                         
                            },

                        extensions: [                   
                            new HardBreak(),                 
                            new Blockquote(),
                            new BulletList(),                        
                            new Heading({ levels: [1, 2, 3] }),                       
                            new ListItem(),
                            new OrderedList(),                        
                            new Bold(),                       
                            new Italic(),                        
                            new Underline(),
                            new History(),                  
                        ],
                        
                        content:this.bodyText[i].title

                    }));
                    }
            }

            this.counter++ 

            },

            deleteTextarea(index){
               
                this.editors[index].destroy()

                this.bodyText[index].title = ''
                this.bodyText[index].hidden = 'd-none'
               
            },

            deleteImagearea(index, imgName) {

                this.bodyText[index].title = ''
                this.bodyText[index].opisanie = ''
                this.bodyText[index].alt = ''
                this.bodyText[index].hidden = 'd-none'

                console.log('картинка удалена из представления');

            },

            addImageareaProp(imgBody) {

                imgBody = imgBody.replace('public', '/storage')
                
                const newTodo = {
                    id: this.counter,
                    title: imgBody,
                    completed:false,
                    type: 'img',
                    current: '',
                    hidden: 'd-block',
                    opisanie: this.$props.itemsopis[this.counter],
                    alt: this.$props.itemsalt[this.counter],
                }

                this.bodyText.push(newTodo);
                    

                this.counter++
            }, 

            addImagearea(index){

               
                
                this.fileCurrent = 1
                this.fileInputChange(index);                

            },

            async fileInputChange(index){
                let files = Array.from(event.target.files);
                this.files0rder = files.slice();
             

                for (let item of files){
                    await this.uploadFile(item, index);                                   
                }
                
            },
            async uploadFile(item, index) {
                let form = new FormData();
               
                
                form.append('imagev', item);

                return await axios.post('/image/upload', form, {
                    onUploadProgress:(itemUpload) => {
                        this.fileProgress = Math.round( (itemUpload.loaded / itemUpload.total) * 100);
                        this.fileCurrent = item.name + ' ' + this.fileProgress;
                    }
                })

                .then(response => {
                    this.fileprogress = 0;
                    this.fileCurrent = '';
                    this.fileFinish.push(item);
                    this.files0rder.splice(item, 1);

                    const newbodyText = {
                        id: this.counter,
                        title: this.counter,
                        completed:false,
                        type: 'img',
                        hidden: 'd-block',
                        opisanie: '',
                        alt: '',
                        }

                         console.log(index);

                    if (index !== undefined ) { 

                        this.bodyText.splice(index, 0, newbodyText);
                        this.bodyText[index].title = '/storage/uploads/' + response.data + '.jpg'

                        index++
                    } else {

                        this.bodyText.splice(this.counter, 0, newbodyText);
                        this.bodyText[this.counter].title = '/storage/uploads/' + response.data + '.jpg'

                    }
               

                

                for (var s in this.editors) {
                    this.editors[s].destroy()
                    delete this.editors[s]   
                }

                for (var i = 0; i < this.bodyText.length; i++) {
                    if (this.bodyText[i].type === 'text') {

                        this.$set(this.editors,  i,  new Editor({

                        onUpdate: ( { state, getHTML, getJSON, transaction } ) => {

                            var curElement = document.activeElement.parentNode.id;
                                                   

                            this.bodyText[curElement].title = getHTML()                 

                         
                        },

                        extensions: [                   
                            new HardBreak(),                 
                            new Blockquote(),
                            new BulletList(),                        
                            new Heading({ levels: [1, 2, 3] }),                       
                            new ListItem(),
                            new OrderedList(),                        
                            new Bold(),                       
                            new Italic(),                        
                            new Underline(),
                            new History(),                  
                        ],
                        content:this.bodyText[i].title

                    }));
                    }
            }

                    

                    this.counter++               
                    
                })

                .catch(error => {
                    console.log(error);
                })
            }
        }

        
    }

</script>

<style>
/************************************************************************************/
.done {
    font-weight:bold;
    size:30px;
    text-decoration: line-through;
}

.image{
        width: 150px;
        height: 150px;
        margin: 0 20px 20px 0;
        background-position: center;
        background-size: contain;
        background-repeat: no-repeat;
        border: 1px dashed #ccc;
        position: relative;
        float: left;
    }
    .image>.name{
        position: absolute;
        bottom: 0;
        text-align: center;
        width: 100%;
        margin: 0 0 2px 0;
        color: #eee;
        text-shadow: 1px 0 1px #e27474, 0 1px 1px #e27474, 0 -1px 1px #e27474, -1px 0 1px #e27474;
    }
    .delete{
        background-image: url(/editor/delete_text.png);
        display: block;
        width: 32px;
        height: 32px;
        position: absolute;
        right: 0px;
        top: 0px;
        background-color: transparent;
        border:none;
    }

    .add{
        background-image: url(/editor/add_text.png);
        display: block;
        width: 32px;
        height: 32px;
        position: absolute;
        top: 0px;
        left:0px;
        background-color: transparent;
        border:none;
    }

    .add:hover{
        transform: rotate(360deg);
        transition: all 2s;
        cursor: pointer;
    }

    .delete:hover{
        transform: rotate(360deg);
        transition: all 2s;
        cursor: pointer;
    }

    .addImage{
        background-image: url(/editor/addimage.png);
        display: block;
        width: 32px;
        height: 32px;
        position: absolute;
        top: 32px;
        left:0px;
        background-color: transparent;
        border:none;
    }

    .addImage:hover{
        transform: rotate(360deg);
        transition: all 2s;
        cursor: pointer;
    }

</style>