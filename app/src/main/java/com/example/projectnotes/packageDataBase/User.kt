package com.example.projectnotes.packageDataBase

class User {
    var id: Int = 0
    private lateinit var username: String
    private lateinit var password: String

    constructor ()

    constructor (id: Int, username: String, password: String) {
        this.id = id
        this.username = username
        this.password = password
    }

    constructor (username: String, password: String) {
        this.username = username
        this.password = password
    }

    fun getID(): Int {
        return this.id
    }

    fun setID(id: Int) {
        this.id = id
    }

    fun getUsername(): String {
        return this.username
    }

    fun setUsername(username: String) {
        this.username = username
    }

    fun getPassword(): String {
        return this.password
    }

    fun setPassword(password: String) {
        this.password = password
    }

}