const state = {
    todoListId: null,
    incompleteTodos: [],
    incompleteTodosPage: 1,
    incompleteTodosLastPage: 1,
    completedTodos: [],
    completedTodosPage: 1,
    completedTodosLastPage: 1,
};

const mutations = {
    cleared (state) {
        state.todoListId = null;
        state.incompleteTodos = [];
        state.completedTodos = [];
        state.incompleteTodosPage =  1;
        state.incompleteTodosLastPage =  1;
        state.compeltedTodosPage =  1;
        state.compeltedTodosLastPage =  1;
    },
    todoListIdSet (state, todoListId) {
        state.todoListId = todoListId;
    },
    incompleteTodosSet (state, data) {
        state.incompleteTodos = data.data;
        state.incompleteTodosPage = data.current_page;
        state.incompleteTodosLastPage = data.last_page;
    },
    completedTodosSet (state, data) {
        state.completedTodos = data.data;
        state.completedTodosPage = data.current_page;
        state.completedTodosLastPage = data.last_page;
    },
};

const actions = {
    clear ({commit}) {
        commit('cleared');
    },
    setTodoListId({commit}, todoListId) {
        commit('todoListIdSet', todoListId);
    },
    viewCompletedTodosPage({commit, state}, page) {
        const query = {
            page: page,
            completed: true,
        };
        Vue.api().todoList(state.todoListId).getTodos(query).then(({data}) => {
            commit('completedTodosSet', data);
        });
    },
    viewIncompleteTodosPage({commit, state}, page) {
        const query = {
            page: page,
            completed: false,
        };
        Vue.api().todoList(state.todoListId).getTodos(query).then(({data}) => {
            commit('incompleteTodosSet', data);
        });
    }
};

const getters = {

};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
