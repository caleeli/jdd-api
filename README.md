# coredump/jdd-api

## Project setup
```
composer require coredump/jdd-api
```

##Examples

Usage inside vue component
```
export default {
    mixins: [ window.ResourceMixin ],
    data() {
        return {
            // GET /api/data/users
            users: this.$api.users.array(), // load list of users in an array
            // GET /api/data/users/1
            user: this.$api.users[1].row(), // load row of user with id=1
            // GET /api/data/users/1/roleObject
            userRole: this.$api.users[1].roleObject.row(), // load row of user relationship "roleObject"
            // GET /api/data/users/1/roleObject/users
            userRoleUsers: this.$api.users[1].roleObject.users.array(), // load array of users of the same role of user 1
            // POST /api/data/users/1 {call:{method:'getNotifications'}, parameters: {...}}
            notifications: this.$api.users[1].arrayCall('getNotifications', {date: today}), // call method "getNotifications" from user 1 model and load its response into an array
            // POST /api/data/users/1 {call:{method:'evaluate'}, parameters: {...}}
            evaluation: this.$api.users[1].rowCall('evaluate', {date: today}), // call method "evaluate" from user 1 model and load its response into a row object
        };
    },
    methods: {
        evaluate(userId) {
            // Call a method "evaluate" from user model with id=1
            this.$api.users[userId].call('evaluate', {date: today}).then(response => {
                console.log('evaluate response: ', response);
            });
        },
        createUser(data = {attributes:{}}) {
            this.$api.users.post(data);
        },
        updateUser() {
            this.$api.users[1].put(this.user);
        },
        deleteUser() {
            this.$api.users[1].delete();
        },
    },
}
```
