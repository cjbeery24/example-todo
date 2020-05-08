import axios from "axios";

export default api();

function api(){

    return {
        install: install,
    };

    function install(Vue, options){

        const appHttp = axios.create({
            baseURL: window.appEnv.baseURL
        });


        Vue.api = _api;

        function _api(){

            return {
                getTodoLists,
                createTodoList,
                todoList,
                todo,
            };
            // Vue.api().getTodoLists(query)
            function getTodoLists(query){
                return appHttp.get('/api/todo-lists', {params: query});
            }
            // Vue.api().createTodoList(query)
            function createTodoList(payload){
                return appHttp.post('/api/todo-lists', payload);
            }
            // Vue.api().todoList(todoListId)
            function todoList(todoListId){
                return {
                    load,
                    getTodos,
                    createTodo,
                    deleteTodoList,
                };
                // Vue.api().todoList(todoListId).load(query)
                function load(query){
                    return appHttp.get('/api/todo-lists/' + todoListId, {params: query});
                }
                // Vue.api().todoList(todoListId).getTodos(query)
                function getTodos(query){
                    query.todo_list_id = todoListId;
                    return appHttp.get('/api/todos', {params: query});
                }
                // Vue.api().todoList(todoListId).createTodo(payload)
                function createTodo(payload){
                    payload.todo_list_id = todoListId;
                    return appHttp.post('/api/todos', payload);
                }
                // Vue.api().todoList(todoListId).deleteTodoList()
                function deleteTodoList(){
                    return appHttp.delete('/api/todo-lists/' + todoListId);
                }
            }
            // Vue.api().todo(todoId)
            function todo(todoId) {
                return {
                    updateTodo,
                    deleteTodo,
                };
                // Vue.api().todo(todoId).updateTodo(payload)
                function updateTodo(payload){
                    return appHttp.put('/api/todos/' + todoId, payload);
                }
                // Vue.api().todo(todoId).deleteTodo()
                function deleteTodo(){
                    return appHttp.delete('/api/todos/' + todoId);
                }
            }
        }
    }
}
