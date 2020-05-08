<template>
    <div>
        <router-link :to="{name: 'lists'}">Back</router-link>
        <h1 v-if="todoList">{{ todoList.name }}</h1>

        <h5>Todos</h5>
        <todos-table :for-completed="false"></todos-table>

        <div v-if="completedTodos.length || completedTodosPage !== 1">
            <h5>Completed Todos</h5>
            <todos-table :for-completed="true"></todos-table>
        </div>

        <h5>Create New Todo</h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="form-control" :class="{'is-invalid': errors.name}" v-model="newTodo.name">
                    <div v-if="errors.name" class="alert alert-danger mt-2">
                        <div v-for="error in errors.name">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" :class="{'is-invalid': errors.description}" rows="4" v-model="newTodo.description"></textarea>
                    <div v-if="errors.description" class="alert alert-danger mt-2">
                        <div v-for="error in errors.description">{{ error }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Target Completion Date</label>
                    <input type="date" class="form-control" :class="{'is-invalid': errors.target_date}" v-model="newTodo.target_date">
                    <div v-if="errors.target_date" class="alert alert-danger mt-2">
                        <div v-for="error in errors.target_date">{{ error }}</div>
                    </div>
                </div>
                <button class="btn btn-primary" @click="createNewTodo">Create New Todo</button>
            </div>
        </div>
    </div>
</template>

<script>
    import TodosTable from "../components/TodosTable";

    export default {
        components: {TodosTable},
        data: function() {
            return {
                todoList: null,
                newTodo: {
                    name: '',
                    description: '',
                    target_date: '',
                },
                errors: {},
            }
        },
        computed: {
            todoListId() {
                return this.$store.state.todos.todoListId;
            },
            completedTodos() {
                return this.$store.state.todos.completedTodos;
            },
            completedTodosPage() {
                return this.$store.state.todos.completedTodosPage;
            },
        },
        beforeRouteUpdate (to, from, next) {
            this.$store.dispatch('todos/setTodoListId', to.params.todoListId);
            this.initialize();
            next();
        },
        mounted() {
            this.$store.dispatch('todos/setTodoListId', this.$route.params.todoListId);
            this.initialize();
        },
        methods: {
            initialize() {
                this.loadTodoList();
                this.$store.dispatch('todos/viewCompletedTodosPage', 1);
                this.$store.dispatch('todos/viewIncompleteTodosPage', 1);
            },
            loadTodoList() {
                Vue.api().todoList(this.todoListId).load().then(({data}) => {
                    this.todoList = data;
                });
            },
            createNewTodo() {
                this.errors = {};
                Vue.api().todoList(this.todoListId).createTodo(this.newTodo).then(({data}) => {
                    this.$store.dispatch('todos/viewIncompleteTodosPage', 1);
                    this.newTodo = {
                        name: '',
                        description: '',
                        target_date: '',
                    }
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                });
            },
        }
    }
</script>
