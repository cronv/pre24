package com.example.projectnotes.packageAdapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.recyclerview.widget.RecyclerView
import com.example.projectnotes.R
import com.example.projectnotes.packageDataBase.Notes
import kotlinx.android.synthetic.main.custom_list_notes.view.*


class AdapterNotes(private val context : Context, private val array : ArrayList<Notes>) :
    RecyclerView.Adapter<AdapterNotes.ViewHolders>(){

    private var listener: IAdapterNotes

    init {
        listener = context as IAdapterNotes
    }

    override fun onBindViewHolder(holder: ViewHolders, position: Int) {
        holder.txtTitle.text = array[position].getTitle()
        holder.txtText.text = array[position].getText()
        holder.txtDate.text = array[position].getDateCreate()
        holder.linearList.setOnClickListener {
            listener.onReturnStartActivity(array[position].getID())
        }
    }

    override fun getItemCount(): Int {
        return array.size
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolders {
        return ViewHolders(
            LayoutInflater.from(context).inflate(
                R.layout.custom_list_notes,
                parent,
                false
            )
        )
    }

    class ViewHolders (view: View) : RecyclerView.ViewHolder(view) {
        val txtTitle : TextView = view.txtTitle
        val txtText : TextView = view.txtText
        val txtDate : TextView = view.txtDate
        val linearList : LinearLayout = view.linearList
    }

    fun swapItems(fromPosition: Int, toPosition: Int) {
        if (fromPosition < toPosition) {
            for (i in fromPosition until toPosition)
                array[i] = array.set(i+1, array[i])
        } else
            for (i in fromPosition..toPosition + 1) {
                array[i] = array.set(i-1, array[i])
        }
        notifyItemMoved(fromPosition, toPosition)

        listener.onRefreshDatabase(array)
    }

    fun removeAt(position: Int) {
        array.removeAt(position)
        notifyItemRemoved(position)
        Toast.makeText(context,"Попал",Toast.LENGTH_SHORT).show()
        listener.onRefreshDatabase(array)
    }

    internal interface IAdapterNotes{
        fun onReturnStartActivity(number : Int)
        fun onRefreshDatabase(arrayNotes : ArrayList<Notes>)
    }
}