// Require the packages we will use:
var http = require("http"),
	socketio = require("socket.io"),
	fs = require("fs");

// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html:
var app = http.createServer(function(req, resp){
	// This callback runs when a new connection is made to our HTTP server.
	
	fs.readFile("chatClient.html", function(err, data){
		// This callback runs when the client.html file has been read from the filesystem.
		
		if(err) return resp.writeHead(500);
		resp.writeHead(200);
		resp.end(data);
	});
});
app.listen(3456);



// Do the Socket.IO magic:
var io = socketio.listen(app);
// data structure for rooms
let rooms = {};
let users = {};

// io.sockets.on('connect', function(socket) {
// 	const sessionID = socket.id;
// 	console.log(sessionID);
// });

// This callback runs when a new Socket.IO connection is established.
io.sockets.on("connection", function(socket){
	console.log(socket.id);
	
	socket.on('message_to_server', function(data) {
		// This callback runs when the server receives a new message from the client.
		
		console.log("message: "+data["message"]); // log it to the Node.JS output
		io.sockets.emit("message_to_client",{message:data["message"] }) // broadcast the message to other users
	});

	// create new private chatroom
	socket.on("loginUser", function(data) {
		if (typeof users[data.user] == "undefined") {
			console.log(users[data.user]);
			console.log("User: " + data.user + " was created with socket.id: " + socket.id)
			let currentUser = {
				id : socket.id,
				curr_room : ""
			}
			users[data.user] = currentUser;
			console.log(users[data.user]);
			socket.emit("loginResults", {success: true});
		}
		else {
			socket.emit("loginResults", {success: false});
		}
	});

	// Delete user from object on logout
	socket.on("logout", function(data) {
		console.log('Deleted User:' + data.user + ' of ID: ' + users[data.user].id);
		delete users[data.user];
	});

	// print list of rooms
	socket.on("getRooms", function() {
		socket.emit("printRooms", {rooms:rooms});
		console.log('Current socket.id is: ' + socket.id);
	});

	// Get specific Room info
	socket.on("joinRoom", function(data) {
		console.log('Current socket.id is: ' + socket.id);
		socket.join(data.room);
		console.log('getThisRoom');
		console.log(rooms[data.room].members);
		rooms[data.room].members[data.username] = socket.id;
		console.log(rooms[data.room].members);
		users[data.username].curr_room = data.room;
		socket.emit("checkJoinRoom", {roomName: data.room, room:rooms[data.room]});
	});

	// leave chatroom
	socket.on("leaveRoom", function(data) {
		console.log("leaveRoom");
		console.log(rooms[data.room].members[data.username]);
		delete rooms[data.room].members[data.username];
		console.log(rooms[data.room].members[data.username]);
		users[data.username].curr_room = "";
		for (var username in rooms[data.room].members) {
			io.to(users[username].id).emit("updateMembers", {room:rooms[data.room]});
		}
	});

	// Add new chat message to room object and relay all current chats back to server
	socket.on("newChat", function(data) {
		console.log('Current socket.id is: ' + socket.id);
		rooms[data.roomName].chats.push(data.message);
		rooms[data.roomName].chatSenders.push(data.sender);
		for (var username in rooms[data.roomName].members) {
			console.log("This username is: " + username);
			console.log("This is the current socketID: " + users[username].id)
			io.to(users[username].id).emit("appendChat", {newChat:data.message, chatSender:data.sender, chatNum:rooms[data.roomName].chats.length});
		}
	});

	// update user list in chatroom
	socket.on("userJoined", function(data) {
		console.log("userJoined");
		console.log(data.room);
		console.log(rooms[data.room]);
		for (var username in rooms[data.room].members) {
			io.to(users[username].id).emit("updateMembers", {room:rooms[data.room]});
		}
	});

	// update homescreen when new room created
	socket.on("updateRoomList", function() {
		console.log("updateRoomList");
		console.log(rooms);
		//socket.emit("printRooms", {rooms: rooms});
		for (var thisUser in users) {
			if (users[thisUser].curr_room == '') {
				io.to(users[thisUser].id).emit("printRooms", {rooms: rooms});
			}
		}
	});

	// kick/ban/pm list
	socket.on("userOptions", function(data) {
		console.log("userOptions");
		console.log("creator - " + rooms[data.room].creator);
		console.log(users[data.user]);
		console.log(users[data.user].id);
		io.to(users[data.user].id).emit("getOptions", {elders: rooms[data.room].elders, overlords:rooms[data.room].overlords, creator:rooms[data.room].creator});
	});

	socket.on("Promote", function(data) {
		if (data.dub == 'elder') {
			rooms[data.room].elders[data.member] = users[data.member];
		}
		else {
			rooms[data.room].overlords[data.member] = users[data.member];
		}
		socket.broadcast.emit("resetUserOptions");
	});

	socket.on("Demote", function(data) {
		if (data.revoke == 'elder') {
			delete rooms[data.room].elders[data.member];
		}
		else {
			delete rooms[data.room].overlords[data.member];
		}
		socket.broadcast.emit("resetUserOptions");
	});

	socket.on("newPM", function(data) {
		console.log("Sending PM to :" + users[data.recipient].id);
		console.log("PM is being sent from:" + data.sender);
		io.to(users[data.recipient].id).emit("sendPM", {message: data.message, sender:data.sender})
	});

	// kick user from chatroom
	socket.on("kick", function(data) {
		console.log("kick");
		io.to(users[data.kickedUser].id).emit("kickThisUser", {room: data.room, initiator: data.initiator});
	});

	// ban user from chatroom
	socket.on("ban", function(data) {
		console.log("ban");
		// add user to ban list for chatroom
		console.log("banned user = " + data.bannedUser);
		console.log(rooms[data.room].banned_users);
		rooms[data.room].banned_users.push(data.bannedUser);
		console.log('banned users: ' + rooms[data.room].banned_users);
		io.to(users[data.bannedUser].id).emit("banThisUser", {room: data.room, initiator: data.initiator});
	});

	// check if user is banned from a chatroom
	socket.on("isBanned", function(data) {
		console.log("isBanned");
		console.log(rooms[data.room].banned_users);
		let checkBan = rooms[data.room].banned_users.includes(data.user);
		console.log(checkBan);
		io.to(users[data.user].id).emit("checkIfBanned", {checkBan: checkBan, room: data.room});
	});

	// Create new chatroom
	socket.on("createRoom", function(data) {
		if (rooms[data.name]) {
			socket.emit("createdRoom", {success:false})
		}
		else {
			let room = {
				privacy : data.privacy,
				creator : data.creator,
				members: {},
				overlords: {},
				elders: {},
				banned_users: [],
				chats: [],
				chatSenders: [],
				password: ""
			}
			room.overlords[data.creator] = socket.id;
			room.elders[data.creator] = socket.id;
			if (data.privacy == "private") {
				room.password = data.password;
			}
			rooms[data.name] = room;
			console.log(rooms[data.name]);
			socket.emit("createdRoom", {success:true, roomName:data.name});
		}
	});

	// https://www.geeksforgeeks.org/web-socket-in-nodejs/
	// when server disconnects from user 
	socket.on('disconnect', ()=>{
		for(var chatUser in users) {
			if(users[chatUser].id == socket.id) {
				if(typeof users[chatUser].curr_room != 'undefined') {
					if (users[chatUser].curr_room != '') {
						console.log(rooms[users[chatUser].curr_room]);
						console.log(rooms[users[chatUser].curr_room].members[chatUser]);
						delete rooms[users[chatUser].curr_room].members[chatUser];
						console.log(rooms[users[chatUser].curr_room].members[chatUser]);
						console.log(rooms[users[chatUser].curr_room]);
					}
				}
				console.log('Disconnected User:' + chatUser + ' of ID: ' + users[chatUser].id);
				delete users[chatUser];
			}
		}
		console.log('disconnected from user');
	}); 
});