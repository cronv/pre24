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
import com.example.projectnotes.packageDataBase.User
import kotlinx.android.synthetic.main.fragment_registration.*
import kotlinx.android.synthetic.main.fragment_registration.view.*

class FragmentRegistration : Fragment() {

    override fun onCreateView(inflater: LayoutInflater, @Nullable container: ViewGroup?, @Nullable savedInstanceState: Bundle?): View? {

        val view = inflater.inflate(R.layout.fragment_registration, container, false)

        view.btnRegistration.setOnClickListener {
            if(view.edtLogin.text.toString() == "" || view.edtPassword.text.toString() == ""){
                Toast.makeText(context,"Вы не ввели логин и/или пароль!",Toast.LENGTH_SHORT).show()
            }
            else{
                val db = DatabaseHandler(context as Context)
                val array = db.allUser
                var check = false

                if(array.count() > 0){
                    for(i in 0 until array.count()){
                        if(array[i].getUsername() == view.edtLogin.text.toString().trim()){
                            Toast.makeText(context,"У Вас уже есть аккаунт с таким именем!",Toast.LENGTH_SHORT).show()
                            check = true
                            break
                        }
                    }
                }
                if(!check){
                    db.addUser(User(edtLogin.text.toString(),edtPassword.text.toString()))
                    Toast.makeText(context,"Аккаунт успешно создан!",Toast.LENGTH_SHORT).show()

                    val intent = Intent(context, NotesActivity::class.java)
                    startActivity(intent)
                }
            }
        }
        return view
    }
}