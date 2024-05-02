package com.example.projectnotes.packageActivity

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.widget.addTextChangedListener
import com.example.projectnotes.R
import com.example.projectnotes.packageDataBase.DatabaseHandler
import com.example.projectnotes.packageDataBase.Notes
import kotlinx.android.synthetic.main.activity_edit_notes.*
import kotlinx.android.synthetic.main.activity_edit_notes.edtTextNotes
import kotlinx.android.synthetic.main.activity_edit_notes.edtTitle
import java.text.SimpleDateFormat
import java.util.*


class EditNotesActivity : AppCompatActivity() {

    private val db = DatabaseHandler(this)
    private var checkEdit = false
    private var number : Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_notes)

        number = intent.getIntExtra("numberList",0)

        val notes = db.getNotes(number)

        edtTitle.setText(notes.getTitle())
        edtTextNotes.setText(notes.getText())

        txtDate.text = String.format("Дата создания: %s",notes.getDateCreate())
        if(notes.getDateEdit().isNotEmpty())
            txtDate.text = String.format("%s\nДата редактирования: %s",txtDate.text,notes.getDateEdit())

        toolbarEditNotes.setNavigationOnClickListener {
            onBackPressed()
        }

        edtTextNotes.addTextChangedListener {
            checkEdit = true
        }
        edtTitle.addTextChangedListener {
            checkEdit = true
        }
        btnSaveNotes.setOnClickListener {
            onUpdateNotes()
        }
    }

    override fun onBackPressed() {
        if(!checkEdit){
            val intent = Intent(this, NotesActivity::class.java)
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            startActivity(intent)
        }else{
            val dialog = AlertDialog.Builder(this)
            dialog.setTitle("Сохранение")
            dialog.setMessage("Сохранить изменения?")
            dialog.setPositiveButton("Сохранить"){ _, _ ->
                onUpdateNotes()
            }
            dialog.setNegativeButton("Удалить"){ _, _ ->
                super.onBackPressed()
            }
            dialog.show()
        }
    }

    private fun onUpdateNotes(){
        if(edtTitle.text.toString().trim() != "") {
            if (edtTextNotes.text.toString().trim() != "") {

                val mCalendar: Calendar = Calendar.getInstance()
                val date = java.lang.String.format(
                    "%s, %s",
                    SimpleDateFormat("dd MMM yyyy", Locale.getDefault()).format(mCalendar.time),
                    SimpleDateFormat("H:mm", Locale.getDefault()).format(mCalendar.time)
                )
                val notes = db.getNotes(number)

                db.updateNotes(
                    Notes(number,
                        edtTextNotes.text.toString(),
                        notes.getDateCreate(),
                        edtTitle.text.toString(),
                        date
                    )
                )

                txtDate.text = String.format("Дата создания: %s\n" +
                        "Дата редактирования: %s",notes.getDateCreate(),date)

                Toast.makeText(this,"Заметка успешно сохранена!",Toast.LENGTH_SHORT).show()

                checkEdit = false
            }
            else Toast.makeText(this,"Вы не ввели текст заметки!", Toast.LENGTH_SHORT).show()
        }
        else Toast.makeText(this,"Вы не ввели название заметки!", Toast.LENGTH_SHORT).show()
    }
}
