<template>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col">Target Date</th>
            <th v-if="forCompleted" scope="col">Completion Date</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="todo in todos">
            <td v-if="!isEditingTodo(todo)">{{ todo.name }}</td>
            <td v-else><input type="text" class="form-control" :class="{'is-invalid': errors.name}" v-model="todoToEdit.name"></td>
            <td v-if="!isEditingTodo(todo)">{{ todo.description }}</td>
            <td v-else><textarea rows="4" class="form-control" :class="{'is-invalid': errors.description}" v-model="todoToEdit.description"></textarea></td>
            <td v-if="!isEditingTodo(todo)">{{ formatDate(todo.target_date) }}</td>
            <td v-else><input type="date" class="form-control" :class="{'is-invalid': errors.target_date}" v-model="todoToEdit.target_date"></td>
            <td v-if="forCompleted">{{ formatDate(todo.completed_at) }}</td>
            <td>
                <button v-if="!forCompleted" class="btn btn-sm btn-success" @click="completeTodo(todo)"><i class="fas fa-check"></i></button>
                <button v-else class="btn btn-sm btn-warning" @click="incompleteTodo(todo)"><i class="fas fa-ban"></i></button>
                <button v-if="!isEditingTodo(todo)" class="btn btn-sm btn-info" @click="editTodo(todo)"><i class="far fa-edit"></i></button>
                <button v-else class="btn btn-sm btn-info" @click="saveTodo"><i class="far fa-save"></i></button>
                <button class="btn btn-sm btn-danger" @click="deleteTodo(todo)"><i class="fas fa-trash"></i></button>
            </td>
        </tr>
        <tr v-if="!todos.length">
            <td :colspan="forCompleted ? 5 : 4" class="text-center">No todos created yet!</td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td :colspan="forCompleted ? 5 : 4">
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
            </td>
        </tr>
        </tfoot>
    </table>
</template>

<script>
    import moment from "moment";

    export default {
        props: {
            forCompleted: {
                type: Boolean,
                required: false,
                default: false,
            }
        },
        data: function() {
            return {
                todoToEdit: {
                    id: null,
                    name: '',
                    description: '',
                    target_date: null,
                    completed_at: null,
                },
                errors: {},
            }
        },
        computed: {
            todos() {
                if (this.forCompleted) {
                    return this.$store.state.todos.completedTodos;
                } else {
                    return this.$store.state.todos.incompleteTodos;
                }
            },
            page() {
                if (this.forCompleted) {
                    return this.$store.state.todos.completedTodosPage;
                } else {
                    return this.$store.state.todos.incompleteTodosPage;
                }
            },
            lastPage() {
                if (this.forCompleted) {
                    return this.$store.state.todos.completedTodosLastPage;
                } else {
                    return this.$store.state.todos.incompleteTodosLastPage;
                }
            }
        },
        methods: {
            viewPage(page) {
                if (this.forCompleted) {
                    this.$store.dispatch('todos/viewCompletedTodosPage', page)
                } else {
                    this.$store.dispatch('todos/viewIncompleteTodosPage', page)
                }
            },
            isEditingTodo(todo) {
                return this.todoToEdit && this.todoToEdit.id === todo.id;
            },
            editTodo(todo) {
                this.todoToEdit = {
                    ...todo
                };
            },
            saveTodo() {
                this.errors = {};
                Vue.api().todo(this.todoToEdit.id).updateTodo(this.todoToEdit).then(() => {
                    this.viewPage(this.page);
                    this.todoToEdit = {
                        id: null,
                        name: '',
                        description: '',
                        target_date: null,
                        completed_at: null,
                    };
                }).catch((error) => {
                    this.errors = error.response.data.errors;
                })
            },
            completeTodo(todo) {
                const payload = {
                    completed_at: moment().format('YYYY-MM-DD')
                }
                Vue.api().todo(todo.id).updateTodo(payload).then(() => {
                    this.$store.dispatch('todos/viewCompletedTodosPage', 1);
                    this.$store.dispatch('todos/viewIncompleteTodosPage', 1);
                });
            },
            incompleteTodo(todo) {
                const payload = {
                    completed_at: null
                }
                Vue.api().todo(todo.id).updateTodo(payload).then(() => {
                    this.$store.dispatch('todos/viewCompletedTodosPage', 1);
                    this.$store.dispatch('todos/viewIncompleteTodosPage', 1);
                });
            },
            deleteTodo(todo) {
                Vue.api().todo(todo.id).deleteTodo().then(({data}) => {
                    this.viewPage(1);
                });
            },
            formatDate(dateString) {
                return moment(dateString).format('M/D/Y')
            },
            formatDateTime(dateString) {
                return moment(dateString).format('M/D/Y h:mm A')
            }
        }
    }
</script>
