<template>
    <div>
        <h1>Todo Lists</h1>

        <div class="row">
            <div class="col">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" :class="{'is-invalid': errors.name}" placeholder="Create new todo list..." v-model="newTodoList.name">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" @click="createTodoList">Create Todo List</button>
                    </div>
                </div>
                <div v-if="errors.name" class="alert alert-danger mt-2">
                    <div v-for="error in errors.name">{{ error }}</div>
                </div>
            </div>
        </div>

        <div v-for="todoList in todoLists" class="row mb-3">
            <div class="col">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-danger mr-3" @click="deleteTodoList(todoList)"><i class="fas fa-trash"></i></button>
                    <router-link :to="{name: 'list-view', params: {todoListId: todoList.id}}">{{ todoList.name }}</router-link>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-4 offset-md-4">
                <div class="d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item">
                            <a :disabled="page === 1" aria-label="Prev" class="page-link" :class="{'disabled': page === 1}" @click="viewPage(page - 1)"><span>&laquo;</span></a>
                        </li>
                        <li v-for="n in lastPage" :key="'page-'+n" :class="{'active': n === page}" class="page-item ripple-parent" @click="viewPage(n)">
                            <a class="page-link">{{ n }}</a>
                        </li>
                        <li class="page-item">
                            <a :disabled="page === lastPage" aria-label="Next" class="page-link" :class="{'disabled': page === lastPage}" @click="viewPage(page + 1)"><span>&raquo;</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                page: 1,
                lastPage: 1,
                todoLists: [],
                newTodoList: {
                    name: '',
                },
                errors: []
            }
        },
        computed: {

        },
        mounted() {
            this.viewPage(1);
        },
        methods: {
            viewPage(page) {
                this.page = page;
                this.loadTodoLists();
            },
            loadTodoLists() {
                const query = {
                    page: this.page
                };
                Vue.api().getTodoLists(query).then(({data}) => {
                    this.todoLists = data.data;
                    this.lastPage = data.last_page;
                });
            },
            createTodoList() {
                this.errors = {};
                Vue.api().createTodoList(this.newTodoList).then(({data}) => {
                    this.newTodoList.name = '';
                    this.viewPage(1);
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                });
            },
            deleteTodoList(todoList) {
                Vue.api().todoList(todoList.id).deleteTodoList().then(({data}) => {
                    this.viewPage(1);
                });
            }
        }
    }
</script>
