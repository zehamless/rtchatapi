 # Title: Real-Time Chat API
 ## Description: A real-time chat API built with Laravel and Pusher.
 ### Author: Zehamless

```markdown
# API Documentation

## Base URL
`http://rtchatapi.test:8080/api`

## Authentication

### Register
**Endpoint**: `/register`  
**Method**: `POST`  
**Description**: Register a new user and generate a token.  
**Request Body**:
```json
{
  "name": "string",
  "email": "string (email format)",
  "password": "string"
}
```
**Responses**:
- `200 OK`: User created successfully.
```json
{
  "message": "User created successfully"
}
```
- `403 Forbidden`: Authorization error.
- `422 Unprocessable Entity`: Validation error.

---

### Login
**Endpoint**: `/login`  
**Method**: `POST`  
**Description**: Log in a user and return an access token.  
**Request Body**:
```json
{
  "email": "string (email format)",
  "password": "string"
}
```
**Responses**:
- `200 OK`: User logged in successfully.
```json
{
  "message": "User logged in successfully",
  "token": "string"
}
```
- `422 Unprocessable Entity`: Validation error.

---

### Logout
**Endpoint**: `/logout`  
**Method**: `POST`  
**Description**: Log out the currently authenticated user.  
**Responses**:
- `200 OK`: User logged out successfully.
```json
{
  "message": "User logged out successfully"
}
```
- `401 Unauthorized`: Authentication error.

---

## Conversations

### Create Conversation
**Endpoint**: `/v1/conversations`  
**Method**: `POST`  
**Description**: Create a new conversation.  
**Request Body**:
```json
{
  "name": "string | null",
  "description": "string | null"
}
```
**Responses**:
- `201 Created`: Conversation created successfully.
```json
{
  "conversation_id": "string"
}
```
- `401 Unauthorized`: Authentication error.
- `422 Unprocessable Entity`: Validation error.
- `500 Internal Server Error`: Failed to create conversation.

### List Conversations
**Endpoint**: `/v1/conversations/{request}`  
**Method**: `GET`  
**Description**: Retrieve conversations.  
**Path Parameter**: `request` - The request ID.  
**Responses**:
- `200 OK`: Array of conversations.
```json
{
  "data": [
    {
      "id": "integer",
      "name": "string",
      "is_group": "boolean",
      "description": "string",
      "created_at": "string (date-time)",
      "updated_at": "string (date-time)"
    }
  ]
}
```
- `401 Unauthorized`: Authentication error.
- `404 Not Found`: Conversation not found.

---

## Messages

### Send Message
**Endpoint**: `/v1/messages`  
**Method**: `POST`  
**Description**: Send a new message in a conversation.  
**Request Body**:
```json
{
  "conversation_id": "integer",
  "receiver_id": "integer",
  "content": "string"
}
```
**Responses**:
- `200 OK`: Message sent successfully.
```json
{
  "data": {
    "id": "integer",
    "content": "string",
    "sent_at": "integer"
  }
}
```
- `401 Unauthorized`: Authentication error.
- `422 Unprocessable Entity`: Validation error.
- `500 Internal Server Error`: Failed to send message.

### List Messages
**Endpoint**: `/v1/messages/{conversation}`  
**Method**: `GET`  
**Description**: Retrieve messages in a conversation.  
**Path Parameter**: `conversation` - The conversation ID.  
**Responses**:
- `200 OK`: Array of messages.
```json
{
  "data": [
    {
      "id": "integer",
      "content": "string",
      "sent_at": "integer"
    }
  ]
}
```
- `401 Unauthorized`: Authentication error.
- `404 Not Found`: Messages not found.

---

### Delete Message
**Endpoint**: `/v1/messages/{message}`  
**Method**: `DELETE`  
**Description**: Delete a message by ID.  
**Path Parameter**: `message` - The message ID.  
**Responses**:
- `200 OK`: Message deleted.
```json
[]
```
- `401 Unauthorized`: Authentication error.
- `404 Not Found`: Message not found.
```
