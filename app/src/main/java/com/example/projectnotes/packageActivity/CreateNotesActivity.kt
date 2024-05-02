package com.example.projectnotes.packageActivity

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import com.example.projectnotes.R
import com.example.projectnotes.packageDataBase.DatabaseHandler
import com.example.projectnotes.packageDataBase.Notes
import kotlinx.android.synthetic.main.activity_create_notes.*
import java.text.SimpleDateFormat
import java.util.*

class CreateNotesActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_create_notes)

        toolbarCreateNotes.setNavigationOnClickListener {
            onBackPressed()
        }
        btnSaveNotes.setOnClickListener {
            onCreateNotes()
        }
    }

    override fun onBackPressed() {
        if(edtTitle.text.toString().trim() != "" || edtTextNotes.text.toString().trim() != ""){
            val dialog = AlertDialog.Builder(this)
            dialog.setTitle("Сохранение")
            dialog.setMessage("Сохранить изменения?")
            dialog.setPositiveButton("Сохранить"){ _, _ ->
                onCreateNotes()
            }
            dialog.setNegativeButton("Удалить"){ _, _ ->
                super.onBackPressed()
            }
            dialog.show()
        }
        else
            super.onBackPressed()
    }

    private fun onCreateNotes(){
        if(edtTitle.text.toString().trim() != ""){
            if(edtTextNotes.text.toString().trim() != ""){
                val db = DatabaseHandler(this)

                val mCalendar: Calendar = Calendar.getInstance()
                val date = java.lang.String.format(
                    "%s, %s",
                    SimpleDateFormat("dd MMM yyyy", Locale.getDefault()).format(mCalendar.time),
                    SimpleDateFormat("H:mm", Locale.getDefault()).format(mCalendar.time)
                )

                db.addNotes(Notes(
                    edtTitle.text.toString(),
                    edtTextNotes.text.toString(),
                    date,""))

                val intent = Intent(this, NotesActivity::class.java)
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                startActivity(intent)

                Toast.makeText(this,"Заметка успешно сохранена!",Toast.LENGTH_SHORT).show()
            }
            else Toast.makeText(this,"Вы не ввели текст заметки!",Toast.LENGTH_SHORT).show()
        }
        else Toast.makeText(this,"Вы не ввели название заметки!",Toast.LENGTH_SHORT).show()
    }
}
