package com.example.projectnotes.packageDataBase

import android.annotation.SuppressLint
import android.content.ContentValues
import android.content.Context
import android.database.sqlite.SQLiteDatabase
import android.database.sqlite.SQLiteOpenHelper

class DatabaseHandler(context: Context) :
    SQLiteOpenHelper(context,
        DATABASE_NAME, null,
        DATABASE_VERSION
    ), IDatabaseHandler {

    override fun onCreate(db: SQLiteDatabase) {
        onCreateNotes(db)
        onCreateUser(db)
    }

    override fun onUpgrade(db: SQLiteDatabase, oldVersion: Int, newVersion: Int) {
        db.execSQL("DROP TABLE IF EXISTS $TABLE_NOTES")
        db.execSQL("DROP TABLE IF EXISTS $TABLE_USER")

        onCreate(db)
    }
    // Notes
    private fun onCreateNotes(db:SQLiteDatabase ){
        val createNotesTable = ("CREATE TABLE " + TABLE_NOTES + "("
                + "$KEY_ID_NOTES INTEGER PRIMARY KEY,"
                + "$KEY_NAME_NOTES TEXT,"
                + "$KEY_DATE_CREATE TEXT,"
                + "$KEY_TITLE TEXT,"
                + "$KEY_DATE_EDIT TEXT)")
        db.execSQL(createNotesTable)
    }

    override val allNotes: ArrayList<Notes>
        @SuppressLint("Recycle")
        get() {
            val notesList = ArrayList<Notes>()
            val selectQuery = "SELECT  * FROM $TABLE_NOTES"

            val db = this.writableDatabase
            val cursor = db.rawQuery(selectQuery, null)

            if (cursor.moveToFirst()) {
                do {
                    val notesTable =
                        Notes()
                    notesTable.setID(Integer.parseInt(cursor.getString(0)))
                    notesTable.setText(cursor.getString(1))
                    notesTable.setDateCreate(cursor.getString(2))
                    notesTable.setTitle(cursor.getString(3))
                    notesTable.setDateEdit(cursor.getString(4))
                    notesList.add(notesTable)
                } while (cursor.moveToNext())
            }

            return notesList
        }

    override val notesCount: Int
        get() {
            val countQuery = "SELECT  * FROM $TABLE_NOTES"
            val db = this.readableDatabase
            val cursor = db.rawQuery(countQuery, null)
            cursor.close()

            return cursor.count
        }

    override fun addNotes(notes: Notes) {
        val db = this.writableDatabase
        val values = ContentValues()
        values.put(KEY_NAME_NOTES, notes.getText())
        values.put(KEY_DATE_CREATE, notes.getDateCreate())
        values.put(KEY_TITLE, notes.getTitle())
        values.put(KEY_DATE_EDIT, notes.getDateEdit())

        db.insert(TABLE_NOTES, null, values)
        db.close()
    }

    @SuppressLint("Recycle")
    override fun getNotes(id: Int): Notes {
        val db = this.readableDatabase

        val cursor = db.query(
            TABLE_NOTES, arrayOf(
                KEY_ID_NOTES,
                KEY_NAME_NOTES,
                KEY_DATE_CREATE,
                KEY_TITLE,
                KEY_DATE_EDIT
            ), "$KEY_ID_NOTES=?",
            arrayOf(id.toString()), null, null, null, null
        )

        cursor?.moveToFirst()

        return Notes(
            Integer.parseInt(cursor!!.getString(0)),
            cursor.getString(1),
            cursor.getString(2),
            cursor.getString(3),
            cursor.getString(4)
        )
    }

    override fun updateNotes(notes: Notes): Int {
        val db = this.writableDatabase

        val values = ContentValues()
        values.put(KEY_NAME_NOTES, notes.getText())
        values.put(KEY_DATE_CREATE, notes.getDateCreate())
        values.put(KEY_TITLE, notes.getTitle())
        values.put(KEY_DATE_EDIT, notes.getDateEdit())

        return db.update(
            TABLE_NOTES, values, "$KEY_ID_NOTES = ?",
            arrayOf(java.lang.String.valueOf(notes.getID()))
        )
    }

    override fun deleteNotes(notes: Notes) {
        val db = this.writableDatabase
        db.delete(TABLE_NOTES, "$KEY_ID_NOTES = ?", arrayOf(java.lang.String.valueOf(notes.getID())))
        db.close()
    }

    override fun deleteAllNotes() {
        val db = this.writableDatabase
        db.delete(TABLE_NOTES, null, null)
        db.close()
    }
    // User
    private fun onCreateUser(db:SQLiteDatabase ){
        val createNotesTable = ("CREATE TABLE " + TABLE_USER + "("
                + "$KEY_ID_USER INTEGER PRIMARY KEY,"
                + "$KEY_USERNAME_USER TEXT,"
                + "$KEY_PASSWORD_USER TEXT)")
        db.execSQL(createNotesTable)
    }

    override val allUser: ArrayList<User>
        @SuppressLint("Recycle")
        get() {
            val userList = ArrayList<User>()
            val selectQuery = "SELECT  * FROM $TABLE_USER"

            val db = this.writableDatabase
            val cursor = db.rawQuery(selectQuery, null)

            if (cursor.moveToFirst()) {
                do {
                    val userTable = User()
                    userTable.setID(Integer.parseInt(cursor.getString(0)))
                    userTable.setUsername(cursor.getString(1))
                    userTable.setPassword(cursor.getString(2))
                    userList.add(userTable)
                } while (cursor.moveToNext())
            }

            return userList
        }

    override val userCount: Int
        get() {
            val countQuery = "SELECT  * FROM $TABLE_USER"
            val db = this.readableDatabase
            val cursor = db.rawQuery(countQuery, null)
            cursor.close()

            return cursor.count
        }

    override fun addUser(user: User) {
        val db = this.writableDatabase
        val values = ContentValues()
        values.put(KEY_USERNAME_USER, user.getUsername())
        values.put(KEY_PASSWORD_USER, user.getPassword())

        db.insert(TABLE_USER, null, values)
        db.close()
    }

    @SuppressLint("Recycle")
    override fun getUser(id: Int): User {
        val db = this.readableDatabase

        val cursor = db.query(
            TABLE_USER, arrayOf(
                KEY_ID_USER,
                KEY_USERNAME_USER,
                KEY_PASSWORD_USER
            ), "$KEY_ID_USER=?",
            arrayOf(id.toString()), null, null, null, null
        )

        cursor?.moveToFirst()

        return User(
            Integer.parseInt(cursor!!.getString(0)),
            cursor.getString(1),
            cursor.getString(2)
        )
    }

    override fun updateUser(user: User): Int {
        val db = this.writableDatabase

        val values = ContentValues()
        values.put(KEY_USERNAME_USER, user.getUsername())
        values.put(KEY_PASSWORD_USER, user.getPassword())

        return db.update(
            TABLE_USER, values, "$KEY_ID_USER = ?",
            arrayOf(java.lang.String.valueOf(user.getID()))
        )
    }

    override fun deleteUser(user: User) {
        val db = this.writableDatabase
        db.delete(TABLE_USER, "$KEY_ID_USER = ?", arrayOf(java.lang.String.valueOf(user.getID())))
        db.close()
    }

    override fun deleteAllUser() {
        val db = this.writableDatabase
        db.delete(TABLE_USER, null, null)
        db.close()
    }

    companion object {

        private const val DATABASE_VERSION = 1
        private const val DATABASE_NAME = "DataBase.db"
        // Notes
        private const val TABLE_NOTES = "Notes"
        private const val KEY_ID_NOTES = "id"
        private const val KEY_NAME_NOTES = "name"
        private const val KEY_DATE_CREATE = "date_create"
        private const val KEY_TITLE = "title_name"
        private const val KEY_DATE_EDIT = "date_edit"
        // User
        private const val TABLE_USER = "User"
        private const val KEY_ID_USER = "id"
        private const val KEY_USERNAME_USER = "username"
        private const val KEY_PASSWORD_USER = "password"
    }
}