package com.example.projectnotes.packageDataBase


class Notes {
    var id: Int = 0
    private lateinit var text: String
    private lateinit var date: String
    private lateinit var title: String
    private lateinit var dateEdit:String

    constructor ()

    constructor (id: Int, text: String, dateCreate: String, title_text:String,date_edit:String) {
        this.id = id
        this.text = text
        this.date = dateCreate
        this.title = title_text
        this.dateEdit = date_edit
    }

    constructor (title_text:String,text: String, dateCreate: String, date_edit:String) {
        this.text = text
        this.date = dateCreate
        this.title = title_text
        this.dateEdit = date_edit
    }

    fun getID(): Int {
        return this.id
    }

    fun setID(id: Int) {
        this.id = id
    }

    fun getTitle(): String {
        return this.title
    }

    fun setTitle(name: String) {
        this.title = name
    }

    fun getText(): String {
        return this.text
    }

    fun setText(text: String) {
        this.text = text
    }

    fun getDateCreate(): String {
        return this.date
    }

    fun setDateCreate(dateCreate: String) {
        this.date = dateCreate
    }

    fun getDateEdit(): String {
        return this.dateEdit
    }

    fun setDateEdit(date_edit: String) {
        this.dateEdit = date_edit
    }
}