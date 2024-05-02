package com.example.projectnotes.packageDataBase


interface IDatabaseHandler {
    // Notes
    val allNotes: List<Notes>
    val notesCount: Int
    fun addNotes(notes: Notes)
    fun getNotes(id: Int): Notes
    fun updateNotes(notes: Notes): Int
    fun deleteNotes(notes: Notes)
    fun deleteAllNotes()
    // User
    val allUser: List<User>
    val userCount: Int
    fun addUser(user: User)
    fun getUser(id: Int): User
    fun updateUser(user: User): Int
    fun deleteUser(user: User)
    fun deleteAllUser()
}