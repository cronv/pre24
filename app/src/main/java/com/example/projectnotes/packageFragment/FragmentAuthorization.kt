package com.example.projectnotes.packageFragment

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.annotation.Nullable
import androidx.fragment.app.Fragment
import com.example.projectnotes.packageActivity.NotesActivity
import com.example.projectnotes.R
import com.example.projectnotes.packageDataBase.DatabaseHandler
import kotlinx.android.synthetic.main.fragment_authorization.view.*

class FragmentAuthorization : Fragment() {

    override fun onCreateView(inflater: LayoutInflater, @Nullable container: ViewGroup?, @Nullable savedInstanceState: Bundle?): View? {

        val view = inflater.inflate(R.layout.fragment_authorization, container, false)

        view.btnAuthorization.setOnClickListener {
            if(view.edtLoginAuth.text.toString() == "" || view.edtPasswordAuth.text.toString() == ""){
                Toast.makeText(context,"Вы не ввели логин и/или пароль!", Toast.LENGTH_SHORT).show()
            }
            else{
                val db = DatabaseHandler(context as Context)
                val array = db.allUser
                var check = true
                if(array.count() > 0){
                    for(i in 0 until array.count()){
                        if(array[i].getUsername() == view.edtLoginAuth.text.toString() &&
                            array[i].getPassword() == view.edtPasswordAuth.text.toString()){

                            check = false

                            val intent = Intent(context,
                                NotesActivity::class.java)
                            startActivity(intent)
                            break
                        }
                    }
                    if(check)
                        Toast.makeText(context,"Вы ввели неверный логин и/или пароль!", Toast.LENGTH_SHORT).show()
                }
            }
        }
        return view
    }
}