package com.example.projectnotes.packageActivity

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import com.example.projectnotes.R
import com.example.projectnotes.packageDataBase.DatabaseHandler
import com.example.projectnotes.packageFragment.FragmentAuthorization
import com.example.projectnotes.packageFragment.FragmentRegistration

class MainActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val db = DatabaseHandler(this)

        if(db.allUser.size > 0)
            supportFragmentManager.beginTransaction().replace(R.id.fragment_container, FragmentAuthorization()).commit()
        else
            supportFragmentManager.beginTransaction().replace(R.id.fragment_container, FragmentRegistration()).commit()
    }
}
