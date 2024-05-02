package com.example.projectnotes.packageActivity

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.Menu
import android.view.View
import android.widget.EditText
import androidx.appcompat.widget.SearchView
import androidx.recyclerview.widget.*
import com.example.projectnotes.R
import com.example.projectnotes.packageAdapter.AdapterNotes
import com.example.projectnotes.packageAdapter.DragManageAdapter
import com.example.projectnotes.packageDataBase.DatabaseHandler
import com.example.projectnotes.packageDataBase.Notes
import kotlinx.android.synthetic.main.activity_notes.*
import java.util.*
import kotlin.collections.ArrayList

class NotesActivity : AppCompatActivity(),
    AdapterNotes.IAdapterNotes {

    private val db = DatabaseHandler(this)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_notes)

        setSupportActionBar(toolbarNotes)


        onCreateAdapter(db.allNotes)
        onCheckCountNotes(db.allNotes.size)
        //====================
        fabAdd.setOnClickListener {
            val intent = Intent(this,
                CreateNotesActivity::class.java)
            startActivity(intent)
        }
    }

    private fun onCheckCountNotes(num : Int){
        if(num > 0)
            txtMessage.visibility = View.GONE
        else
            txtMessage.visibility = View.VISIBLE

        txtMessage.setText(R.string.text_list_empty)
    }

    override fun onBackPressed() {
        finishAffinity()
    }

    override fun onReturnStartActivity(number: Int) {
        val intent = Intent(this, EditNotesActivity::class.java)
        intent.putExtra("numberList", number)
        startActivity(intent)
    }

    override fun onRefreshDatabase(arrayNotes: ArrayList<Notes>) {
        db.deleteAllNotes()

        for(i in 0 until arrayNotes.count())
            db.addNotes(arrayNotes[i])

        onCheckCountNotes(db.allNotes.size)
    }

    override fun onCreateOptionsMenu(menu: Menu?): Boolean {
        menuInflater.inflate(R.menu.menu_notes_search,menu)
        val searchItem = menu!!.findItem(R.id.menu_search)
        if(searchItem != null){
            val searchView = searchItem.actionView as SearchView

            val editText = searchView.findViewById<EditText>(R.id.search_src_text)
            editText.setHint(R.string.text_search)

            searchView.setOnQueryTextListener(object : SearchView.OnQueryTextListener{
                override fun onQueryTextSubmit(query: String?): Boolean {
                    return true
                }

                override fun onQueryTextChange(newText: String?): Boolean {

                    if(editText.text.toString() != "") {
                        val notes : ArrayList<Notes> = arrayListOf()
                        val totalNotes = db.allNotes

                        for (i in 0 until totalNotes.count()) {
                            val num =
                                substring(totalNotes[i].getText().toLowerCase(Locale.getDefault()),
                                    editText.text.toString().toLowerCase(Locale.getDefault())
                                )
                            if (num != -1)
                                notes.add(totalNotes[i])
                        }
                        listNotes.visibility = View.INVISIBLE
                        listNotesFind.visibility = View.VISIBLE
                        listNotesFind.layoutManager = LinearLayoutManager(this@NotesActivity)
                        listNotesFind.adapter = AdapterNotes(this@NotesActivity, notes)

                        if(notes.count() == 0){
                            txtMessage.text = "По вашему запросу ничего не найдено"
                            txtMessage.visibility = View.VISIBLE
                        }
                        else txtMessage.visibility = View.GONE
                    }
                    else {
                        listNotes.visibility = View.VISIBLE
                        listNotesFind.visibility = View.INVISIBLE

                        listNotesFind.layoutManager = LinearLayoutManager(this@NotesActivity)
                        listNotesFind.adapter = AdapterNotes(this@NotesActivity, db.allNotes)
                        onCheckCountNotes(db.allNotes.size)
                    }

                    return true
                }

            })
        }
        return true
    }

    private fun substring(string: String, subString: String): Int {
        if (string.length < subString.length) return -1
        var patternHash = 0
        var currentHash = 0
        for (i in subString.indices) {
            patternHash += subString[i].toInt()
            currentHash += string[i].toInt()
        }
        val end = string.length - subString.length + 1
        for (i in 0 until end) {
            if (patternHash == currentHash) if (string.regionMatches(
                    i,
                    subString,
                    0,
                    subString.length
                )
            ) return i
            currentHash -= string[i].toInt()
            if (i != end - 1) currentHash += string[i + subString.length].toInt()
        }
        return -1
    }

    private fun onCreateAdapter(arrayNotes: ArrayList<Notes>){
        listNotes.layoutManager = LinearLayoutManager(this)
        val itemAdapter = AdapterNotes(this, arrayNotes)
        listNotes.adapter = itemAdapter

        val callback = object : DragManageAdapter(this, itemAdapter, ItemTouchHelper.UP.or(ItemTouchHelper.DOWN), ItemTouchHelper.LEFT){
            override fun onSwiped(viewHolder: RecyclerView.ViewHolder, direction: Int) {
                val adapter = listNotes.adapter as AdapterNotes
                adapter.removeAt(viewHolder.adapterPosition)
            }
        }
        //=============
        val helper = ItemTouchHelper(callback)
        helper.attachToRecyclerView(listNotes)
    }
}
