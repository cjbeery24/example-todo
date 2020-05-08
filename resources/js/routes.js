import NotFound from "./views/NotFoundView";

import BaseView from "./views/BaseView";
import TodoListsView from "./views/TodoListsView";
import TodoListView from "./views/TodoListView";

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            component: BaseView,
            children: [
                {
                    path: '/',
                    component: TodoListsView,
                    name: 'lists'

                },
                {
                    path: 'lists/:todoListId',
                    component: TodoListView,
                    name: 'list-view'

                },
                {
                    path: '*',
                    component: NotFound,
                },
            ]
        },
    ]
}
